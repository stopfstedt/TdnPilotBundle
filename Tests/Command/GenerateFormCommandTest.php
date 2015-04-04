<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateFormCommand;
use Tdn\PilotBundle\Manipulator\FormManipulator;
use Tdn\PilotBundle\Model\File;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\FormData;

/**
 * Class GenerateFormCommandTest
 * @package Tdn\PilotBundle\Tests\Command
 */
class GenerateFormCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @return GenerateFormCommand
     */
    protected function getCommand()
    {
        return new GenerateFormCommand();
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
     * @return Mockery\MockInterface|FormManipulator
     */
    protected function getManipulator()
    {
        $manipulator = Mockery::mock(
            new FormManipulator()
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
        $formTypeFileMock  = $this->getFormTypeMock();
        $exceptionFileMock = $this->getExceptionFileMock();

        return [
            $formTypeFileMock->getRealPath() => $formTypeFileMock,
            $exceptionFileMock->getRealPath() => $exceptionFileMock
        ];
    }

    /**
     * @return File
     */
    protected function getFormTypeMock()
    {
        $formTypeFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $formTypeFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents' => FormData::FOO_FORM_TYPE,
                    'getBaseName'  => 'FooType',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Form' .
                        DIRECTORY_SEPARATOR . 'Type',
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type' .
                        DIRECTORY_SEPARATOR . 'FooType.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $formTypeFileMock;
    }

    /**
     * @return File
     */
    protected function getExceptionFileMock()
    {
        $exceptionFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $exceptionFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => FormData::FORM_EXCEPTION,
                    'getBaseName'  => 'InvalidFormException',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Exception',
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR . 'InvalidFormException.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $exceptionFileMock;
    }
}
