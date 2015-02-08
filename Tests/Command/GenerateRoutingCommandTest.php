<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateRoutingCommand;
use Tdn\PilotBundle\Manipulator\RoutingManipulator;
use Tdn\PilotBundle\Model\GeneratedFile;
use \Mockery;

/**
 * Class GenerateRoutingCommandTest
 * @package Tdn\PilotBundle\Tests\Command
 */
class GenerateRoutingCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @return GenerateRoutingCommand
     */
    protected function getCommand()
    {
        return new GenerateRoutingCommand();
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
            '--route-prefix'     => 'v1',
            '--remove'           => false,
            'entity'             => 'FooBarBundle:Foo',
            'routing-file'       => 'routing.yml'
        ];
    }

    /**
     * @return Mockery\MockInterface|RoutingManipulator
     */
    protected function getManipulator()
    {
        $manipulator = Mockery::mock(
            new RoutingManipulator($this->getOutputEngine(), $this->getBundle(), $this->getMetadata())
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
        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'routing.out'
        );

        $routingFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'routing',
                    'getPath'      => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Resources' .
                        DIRECTORY_SEPARATOR . 'config',
                    'getExtension' => 'yml',
                    'getContents'  => $content,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Resources' .
                        DIRECTORY_SEPARATOR . 'config' .
                        DIRECTORY_SEPARATOR . 'routing.yml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return [
            $routingFileMock->getFullPath() => $routingFileMock
        ];
    }
}
