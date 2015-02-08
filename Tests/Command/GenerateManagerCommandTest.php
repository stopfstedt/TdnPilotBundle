<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateManagerCommand;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;
use Tdn\PilotBundle\Model\GeneratedFile;
use \Mockery;

/**
 * Class GenerateManagerCommandTest
 * @package Tdn\PilotBundle\Tests\Command
 */
class GenerateManagerCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @return GenerateManagerCommand
     */
    protected function getCommand()
    {
        return new GenerateManagerCommand();
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
            'entity'             => 'FooBarBundle:Foo'
        ];
    }

    /**
     * @return Mockery\MockInterface|ManagerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = Mockery::mock(
            new ManagerManipulator($this->getOutputEngine(), $this->getBundle(), $this->getMetadata())
        );

        $manipulator
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'prepare'  => $manipulator,
                    'isValid'  => true,
                    'generate' => $this->getGeneratedFiles()
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $manipulator;
    }

    /**
     * @return Mockery\MockInterface|GeneratedFile[]
     */
    protected function getGeneratedFiles()
    {
        $managerContent = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'manager.out'
        );

        $mgrFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $mgrFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooManager',
                    'getPath'      => $this->getOutDir(),
                    'getExtension' => 'php',
                    'getContents'  => $managerContent
                ]
            )
            ->zeroOrMoreTimes()
        ;

        $mgrInterfaceContent = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'manager.interface.data'
        );

        $mgrInterfaceFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $mgrInterfaceFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooManagerInterface',
                    'getPath'      => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Entity' .
                        DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getContents'  => $mgrInterfaceContent,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Entity' .
                        DIRECTORY_SEPARATOR . 'Manager' .
                        DIRECTORY_SEPARATOR . 'FooManagerInterface.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return [
            $mgrFileMock->getFullPath() => $mgrFileMock,
            $mgrInterfaceFileMock->getFullPath() => $mgrInterfaceFileMock
        ];
    }
}
