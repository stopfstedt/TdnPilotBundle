<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Finder\SplFileInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Model\FileInterface;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\PilotBundle\Template\Strategy\TwigStrategy;
use Tdn\PilotBundle\TdnPilotBundle;
use \Mockery;

/**
 * Class AbstractManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
abstract class AbstractManipulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $outDir;

    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var TemplateStrategyInterface
     */
    private $templateStrategy;

    /**
     * @var ClassMetadata
     */
    private $metadata;

    /**
     * @return ManipulatorInterface
     */
    abstract protected function getManipulator();

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    abstract protected function getFileDependencies();

    /**
     * @return ArrayCollection|FileInterface[]
     */
    abstract protected function getGeneratedFiles();

    protected function setUp()
    {
        $this->outDir       = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tdn-pilot';
        $this->filesystem   = new Filesystem();
        $this->filesystem->remove($this->outDir);
        $this->filesystem->mkdir($this->outDir);
    }

    public function testOutputEngine()
    {
        $this->assertEquals($this->getTemplateStrategy(), $this->getManipulator()->getTemplateStrategy());
    }

    public function testBundle()
    {
        $this->assertEquals($this->getBundle(), $this->getManipulator()->getBundle());
    }

    public function testEntity()
    {
        $this->assertEquals('Foo', $this->getManipulator()->getEntity());
    }

    public function testEntityNamespace()
    {
        $this->assertEquals('', $this->getManipulator()->getEntityNamespace());
    }

    public function testMetadata()
    {
        $this->assertEquals($this->getMetadata(), $this->getManipulator()->getMetadata());
    }

    public function testFileDependencies()
    {
        $this->assertEquals($this->getFileDependencies(), $this->getManipulator()->getFileDependencies());
    }

    public function testTargetDirectory()
    {
        $this->assertEquals($this->getOutDir(), $this->getManipulator()->getTargetDirectory());
    }

    public function testMessages()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals($this->getExpectedMessages(), $manipulator->getMessages());
    }

    public function testOverwrite()
    {
        $this->assertEquals(false, $this->getManipulator()->shouldOverwrite());
        $manipulator = $this->getManipulator();
        $manipulator->setOverwrite(true);
        $this->assertEquals(true, $manipulator->shouldOverwrite());
    }

    public function testGeneratedFiles($type = null)
    {
        $manipulator = $this->getManipulator();
        $manipulator->setTargetDirectory($this->getOutDir()); //Ensure test directory
        $manipulator->prepare(); //Set up generated file objects.
        $manipulator->setFileDependencies(new ArrayCollection()); //Dependencies will have to be ignored.
        $this->assertEquals(true, $manipulator->isValid()); //And now should be valid...
        $generatedFiles = $manipulator->generate();

        foreach ($generatedFiles as $generatedFile) {
            $expectedContents = $this->getGeneratedFiles($type)[$generatedFile->getFullPath()]->getContents();
            $this->assertEquals(
                $expectedContents,
                $generatedFile->getContents(),
                'Contents don\'t match. File: ' . $generatedFile->getFilename()
            );
        }
    }

    /**
     * @return void
     */
    protected function setBundle()
    {
        $this->bundle = Mockery::mock('\Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $this->bundle
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getPath'      => $this->getOutDir(),
                    'getNamespace' => 'Foo\\BarBundle',
                    'getName'      => 'FooBarBundle'
                ]
            )
            ->zeroOrMoreTimes()
        ;
    }

    /**
     * @return BundleInterface
     */
    protected function getBundle()
    {
        if (null === $this->bundle) {
            $this->setBundle();
        }

        return $this->bundle;
    }

    /**
     * @return void
     */
    protected function setTemplateStrategy()
    {
        $this->templateStrategy = new TwigStrategy();
        $this->templateStrategy->setSkeletonDirs($this->getSkeletonDirs());
    }

    /**
     * @return TemplateStrategyInterface
     */
    protected function getTemplateStrategy()
    {
        if (null === $this->templateStrategy) {
            $this->setTemplateStrategy();
        }

        return $this->templateStrategy;
    }

    /**
     * @return void
     */
    protected function setMetadata()
    {
        $this->metadata = Mockery::mock('\Doctrine\ORM\Mapping\ClassMetadata');
        $this->metadata
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'isIdentifierNatural' => true,
                    'getReflectionClass'  => new \ReflectionClass(new \stdClass())
                ]
            )
            ->zeroOrMoreTimes()
        ;

        if ($this->metadata instanceof ClassMetadata) {
            $this->metadata->name       = 'Foo\BarBundle\Entity\Foo';
            $this->metadata->identifier = ['id'];
            $this->metadata->associationMappings = [];
            $this->metadata->namespace = 'Foo\BarBundle\Entity';
            $this->metadata->fieldMappings = [
                'id' => [
                    'fieldName'  => 'id',
                    'type'       => 'integer',
                    'columnName' => 'id',
                    'id'         => true
                ],
                'description' => [
                    'fieldName'  => 'description',
                    'type'       => 'string',
                    'columnName' => 'description'
                ],
                'name' => [
                    'fieldName'  => 'name',
                    'type'       => 'string',
                    'columnName' => 'name'
                ],
                'title' => [
                    'fieldName'  => 'title',
                    'type'       => 'string',
                    'columnName' => 'title'
                ]
            ];
        }
    }

    /**
     * @return ClassMetadata
     */
    protected function getMetadata()
    {
        if (null === $this->metadata) {
            $this->setMetadata();
        }

        return $this->metadata;
    }

    /**
     * @return string[]
     */
    protected function getSkeletonDirs()
    {
        $bundleClass    = new \ReflectionClass(new TdnPilotBundle());
        $skeletonDirs   = [];
        $skeletonDirs[] = dirname($bundleClass->getFileName()) . '/Resources/skeleton';
        $skeletonDirs[] = dirname($bundleClass->getFileName()) . '/Resources';

        return $skeletonDirs;
    }

    /**
     * @return string
     */
    protected function getOutDir()
    {
        return $this->outDir;
    }

    /**
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        return $this->filesystem;
    }

    protected function getExpectedMessages()
    {
        return new ArrayCollection();
    }

    protected function tearDown()
    {
        $this->filesystem->remove($this->outDir);
        Mockery::close();
    }
}
