<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateHandlerCommand;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

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
    protected function getOptions()
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
            new HandlerManipulator($this->getTemplateStrategy(), $this->getBundle(), $this->getMetadata())
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
     * @return GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $handlerFileMock = $this->getHandlerFileMock();
        $handlerServiceMock = $this->getHandlerServiceMock();

        return [
            $handlerFileMock->getFullPath() => $handlerFileMock,
            $handlerServiceMock->getFullPath() => $handlerServiceMock
        ];
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getHandlerFileMock()
    {
        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'handler.out'
        );

        $handlerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $handlerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooHandler',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Handler',
                    'getExtension' => 'php',
                    'getContents'  => $content,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . 'FooHandler.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlerFileMock;
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getHandlerServiceMock()
    {

        $handlrServContent = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'handlers.service.xml.out'
        );

        $handlrServMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'handlers',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getExtension' => 'xml',
                    'getContents'  => $handlrServContent,
                    'getFullPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }
}
