<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateControllerCommand;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

/**
 * Class GenerateControllerCommandTest
 * @package Tdn\PilotBundle\Test\Command
 */
class GenerateControllerCommandTest extends AbstractGeneratorCommandTest
{
//    /**
//     * @return void
//     */
//    public function testRoutePrefix()
//    {
//        /** @var GenerateControllerCommand $command */
//        $command = $this->getFullCommand();
//        $command->setEntity('Foo');
//        $this->assertEquals(strtolower($command->getEntity()), $command->getRoutePrefix());
//        $this->assertEquals('test', $command->getRoutePrefix('test'));
//    }

    /**
     * @return GenerateControllerCommand
     */
    protected function getCommand()
    {
        return new GenerateControllerCommand();
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            'command'            => $this->getCommand()->getName(),
            '--overwrite'        => false,
            '--target-directory' => $this->getOutDir(),
            '--resource'         => true,
            '--with-swagger'     => true,
            '--entity'           => 'FooBarBundle:Foo'
        ];
    }

    /**
     * @return Mockery\MockInterface|ControllerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = Mockery::mock(
            new ControllerManipulator()
        );

        $manipulator
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'prepare'  => $manipulator,
                    'isValid'  => true,
                    'generate' => $this->getGeneratedFiles(),
                ]
            )
            ->zeroOrMoreTimes()
        ;

        /** @var ManipulatorInterface $manipulator */
        $manipulator->setMetadata($this->getMetadata());
        $manipulator->setTemplateStrategy($this->getTemplateStrategy());
        $manipulator->setBundle($this->getBundle());
        $manipulator->setTargetDirectory($this->getOutDir());

        return $manipulator;
    }

    /**
     * @return GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $controllerFileMock = $this->getControllerFileMock();

        return [
            $controllerFileMock->getFullPath() => $controllerFileMock
        ];
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getControllerFileMock()
    {
        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'basic.controller.out'
        );

        $controllerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooController',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
                    'getExtension' => 'php',
                    'getContents'  => $content,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooController.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }
}
