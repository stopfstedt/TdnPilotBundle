<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateManagerCommand;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;
use Tdn\PilotBundle\Model\File;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\ManagerData;

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
    public function getOptions()
    {
        return [
            'command'            => $this->getCommand()->getName(),
            '--overwrite'        => false,
            '--target-directory' => $this->getOutDir(),
            '--entity'           => 'FooBarBundle:Foo'
        ];
    }

    /**
     * @return Mockery\MockInterface|ManagerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = Mockery::mock(
            new ManagerManipulator()
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
     * @return File[]
     */
    protected function getGeneratedFiles()
    {
        $managerFileMock      = $this->getManagerFileMock();
        $mgrInterfaceFileMock = $this->getMgrInterfaceFileMock();
        $mgrServiceMock       = $this->getManagerServiceMock();

        return [
            $managerFileMock->getRealPath()      => $managerFileMock,
            $mgrInterfaceFileMock->getRealPath() => $mgrInterfaceFileMock,
            $mgrServiceMock->getRealPath()       => $mgrServiceMock
        ];
    }

    /**
     * @return File
     */
    protected function getManagerFileMock()
    {
        $managerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $managerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => ManagerData::FOO_MANAGER,
                    'getBaseName'  => 'FooManager',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManager.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $managerFileMock;
    }

    /**
     * @return File
     */
    protected function getMgrInterfaceFileMock()
    {
        $mgrInterfaceFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $mgrInterfaceFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => ManagerData::FOO_MANAGER_INTERFACE,
                    'getBaseName'  => 'FooManagerInterface',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManagerInterface.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $mgrInterfaceFileMock;
    }

    /**
     * @return File
     */
    protected function getManagerServiceMock()
    {
        $mgrServiceMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $mgrServiceMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => ManagerData::FOO_MANAGER_SERVICE_XML,
                    'getBaseName'  => 'managers',
                    'getExtension' => 'xml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.xml'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }
}
