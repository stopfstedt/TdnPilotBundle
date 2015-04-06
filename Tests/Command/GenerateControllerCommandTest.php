<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateControllerCommand;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use Tdn\PilotBundle\Model\File;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\ControllerData;

/**
 * Class GenerateControllerCommandTest
 * @package Tdn\PilotBundle\Test\Command
 */
class GenerateControllerCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @return void
     */
    public function testRoutePrefix()
    {
        /** @var GenerateControllerCommand $command */
        $command = $this->getFullCommand();
        $command->setEntity('Foo');
        $this->assertEquals(strtolower($command->getEntity()), $command->getRoutePrefix());
        $this->assertEquals('test', $command->getRoutePrefix('test'));
    }

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
    public function getOptions()
    {
        return $this->optionsProvider();
    }

    /**
     * @return array
     */
    protected function optionsProvider()
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
     * @return array
     */
    protected function altOptionsProvider()
    {
        return [
            'command'            => $this->getCommand()->getName(),
            '--overwrite'        => false,
            '--target-directory' => $this->getOutDir(),
            '--resource'         => true,
            '--with-swagger'     => true,
            '--entities-location'=> '/path/to/fake/entities'
        ];
    }

    /**
     * @return ControllerManipulator
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

        return $manipulator;
    }

    /**
     * @return File[]
     */
    protected function getGeneratedFiles()
    {
        $controllerFileMock = $this->getControllerFileMock();

        return [
            $controllerFileMock->getRealPath() => $controllerFileMock
        ];
    }

    /**
     * @return File
     */
    protected function getControllerFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => ControllerData::BASIC_FOO_CONTROLLER,
                    'getFilename'  => 'FooController',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
                    'getExtension' => 'php',
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
