<?php

namespace Tdn\PilotBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\FormatterHelper;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Command\AbstractGeneratorCommand;
use Tdn\PilotBundle\Template\Strategy\TwigStrategy;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Services\Doctrine\EntityUtils;
use \Mockery as Mockery;

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
     * @var TemplateStrategyInterface
     */
    private $templateStrategy;

    /**
     * @var ClassMetadata
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
     * @return File[]
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
                $this->getTemplateStrategy(),
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
        $container->set('tdn_pilot.template.strategy.default', $this->getTemplateStrategy());

        return $container;
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
     * @return void
     */
    protected function setTemplateStrategy()
    {
        $this->templateStrategy = Mockery::mock(new TwigStrategy());
        $this->templateStrategy->shouldDeferMissing();
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
        $command->setEntityUtils($this->getEntityUtils());
        $command->setManipulator($this->getManipulator());
        $command->setHelperSet($this->getHelperSet('y'));
        $command->setContainer($this->getContainer());

        return $command;
    }

    /**
     * @return EntityUtils
     */
    protected function getEntityUtils()
    {
        $entityUtils = Mockery::mock('\Tdn\PilotBundle\Services\Doctrine\EntityUtils');
        $entityUtils
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getMetadata' => $this->getMetadata()
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $entityUtils;
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
