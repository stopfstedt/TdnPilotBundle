<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateHandlerCommand;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;
use Tdn\PilotBundle\Model\File;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\HandlerData;

/**
 * Class GenerateHandlerCommandTest
 * @package Tdn\PilotBundle\Tests\Command
 */
class GenerateHandlerCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @return GenerateHandlerCommand
     */
    protected function getCommand()
    {
        return new GenerateHandlerCommand();
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
     * @return Mockery\MockInterface|HandlerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = Mockery::mock(
            new HandlerManipulator()
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
        $handlerFileMock = $this->getHandlerFileMock();
        $handlerServiceMock = $this->getHandlerServiceMock();

        return [
            $handlerFileMock->getRealPath() => $handlerFileMock,
            $handlerServiceMock->getRealPath() => $handlerServiceMock
        ];
    }

    /**
     * @return File
     */
    protected function getHandlerFileMock()
    {
        $handlerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $handlerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents' => HandlerData::FOO_HANDLER,
                    'getBaseName'  => 'FooHandler',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Handler',
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . 'FooHandler.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $handlerFileMock;
    }

    /**
     * @return File
     */
    protected function getHandlerServiceMock()
    {
        $handlrServMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents' => HandlerData::FOO_HANDLER_SERVICE_YAML,
                    'getBaseName'  => 'handlers',
                    'getExtension' => 'yaml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.yaml'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }
}
