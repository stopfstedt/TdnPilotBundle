<?php

namespace Tdn\PilotBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\Input;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PilotBundle\Command\AbstractGeneratorCommand;
use Tdn\PilotBundle\OutputEngine\TwigOutputEngine;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Model\GeneratedFile;
use Tdn\PilotBundle\TdnPilotBundle;
use \Mockery as Mockery;
use \InvalidArgumentException;

/**
 * Class AbstractGeneratorCommandTest
 * @package Tdn\PilotBundle\Tests\Command
 */
abstract class AbstractGeneratorCommandTest extends GenerateCommandTest
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var OutputEngineInterface
     */
    private $outputEngine;

    /**
     * @var ClassMetadataInfo
     */
    private $metadata;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    protected $outDir;

    /**
     * Create manipulator object.
     * @return ManipulatorInterface
     */
    abstract protected function getManipulator();

    /**
     * @return AbstractGeneratorCommand
     */
    abstract protected function getCommand();

    /**
     * @return array
     */
    abstract protected function getOptions();

    /**
     * @return GeneratedFile[]
     */
    abstract protected function getGeneratedFiles();

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->outDir       = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tdn-pilot';
        $this->filesystem   = new Filesystem();
        $this->filesystem->remove($this->outDir);
        $this->filesystem->mkdir($this->outDir);
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $tester = new CommandTester($this->getFullCommand());
        $tester->execute($this->getOptions());

        foreach ($this->getGeneratedFiles() as $generatedFile) {
            $this->assertRegExp('#' . $generatedFile->getFullPath() . '#', $tester->getDisplay());
        }
    }

    /**
     * @return void
     */
    public function testManipulator()
    {
        $command = $this->getFullCommand();
        $this->assertEquals(
            $this->getManipulator(),
            $command->getManipulator(
                $this->getInput(),
                $this->getOutputEngine(),
                $this->getBundle(),
                $this->getMetadata()
            )
        );
    }

    /**
     * @return void
     */
    public function testEntity()
    {
        $command = $this->getFullCommand();
        $command->setEntity('Foo');
        $this->assertEquals('Foo', $command->getEntity());
    }

    /**
     * @return void
     */
    public function testShortcutNotation()
    {
        $command = $this->getFullCommand();
        $bundle  = 'BarBundle';
        $entity  = 'Foo';
        $this->assertEquals([$bundle, $entity], $command->parseShortcutNotation('BarBundle:Foo'));
    }

    /**
     * @expectedException              InvalidArgumentException
     * @expectedExceptionMessageRegExp #The entity name must contain a :#
     */
    public function testBadShortcutNotation()
    {
        $command = $this->getFullCommand();
        $command->parseShortcutNotation('BarBundle');
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        $container = parent::getContainer();
        $registry = Mockery::mock('\Symfony\Bridge\Doctrine\ManagerRegistry');
        $registry
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getAliasNamespace' => 'Foo\BarBundle\Entity',
                ]
            )
            ->zeroOrMoreTimes()
        ;

        $container->set('doctrine', $registry);
        $container->set('tdn_pilot.output.engine.default', $this->getOutputEngine());

        return $container;
    }

    /**
     * @return void
     */
    protected function setMetadata()
    {
        $this->metadata = Mockery::mock('\Doctrine\ORM\Mapping\ClassMetadataInfo');
        $this->metadata
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getName' => 'Foo',
                    'isIdentifierNatural' => true,
                ]
            )
            ->zeroOrMoreTimes()
        ;

        if ($this->metadata instanceof ClassMetadataInfo) {
            $this->metadata->identifier = ['id'];
            $this->metadata->associationMappings = [];
            $this->metadata->namespace = '';
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
                ]
            ];
        }
    }

    /**
     * @return ClassMetadataInfo
     */
    protected function getMetadata()
    {
        if (null === $this->metadata) {
            $this->setMetadata();
        }

        return $this->metadata;
    }

    /**
     * @return void
     */
    protected function setOutputEngine()
    {
        $this->outputEngine = Mockery::mock(new TwigOutputEngine());
        $this->outputEngine
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getSkeletonDirs' => $this->getSkeletonDirs()
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes();
    }

    /**
     * @return OutputEngineInterface
     */
    protected function getOutputEngine()
    {
        if (null === $this->outputEngine) {
            $this->setOutputEngine();
        }

        return $this->outputEngine;
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
                    'getPath' => $this->getOutDir(),
                    'getNamespace' => 'Foo\\BarBundle',
                    'getName' => 'FooBarBundle'
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
     * @param  string    $input
     * @return HelperSet
     */
    protected function getHelperSet($input)
    {
        $formatter = new FormatterHelper();
        $question = new QuestionHelper();
        $question->setInputStream($this->getInputStream($input));

        return new HelperSet([$formatter, $question]);
    }

    /**
     * @return Mockery\MockInterface|Input
     */
    protected function getInput()
    {
        $input = Mockery::mock('\Symfony\Component\Console\Input\Input');
        $input
            ->shouldDeferMissing()
        ;

        return $input;
    }

    /**
     * @return Application
     */
    protected function getApplication()
    {
        $application = new Application();
        $application->add($this->getCommand());

        return $application;
    }

    /**
     * @return AbstractGeneratorCommand
     */
    protected function getFullCommand()
    {
        /** @var AbstractGeneratorCommand $command */
        $command = $this->getApplication()->find($this->getCommand()->getName());
        $command->setManipulator($this->getManipulator());
        $command->setHelperSet($this->getHelperSet('y'));
        $command->setContainer($this->getContainer());
        $command->setMetadata($this->getMetadata());

        return $command;
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        $this->filesystem->remove($this->outDir);
        Mockery::close();
    }
}
