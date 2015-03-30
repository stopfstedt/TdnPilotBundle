<?php

namespace Tdn\PilotBundle\Tests\Command;

use Tdn\PilotBundle\Command\GenerateFormCommand;
use Tdn\PilotBundle\Manipulator\FormManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
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
     * @return GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $formTypeFileMock  = $this->getFormTypeMock();
        $exceptionFileMock = $this->getExceptionFileMock();

        return [
            $formTypeFileMock->getFullPath() => $formTypeFileMock,
            $exceptionFileMock->getFullPath() => $exceptionFileMock
        ];
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getFormTypeMock()
    {
        $formTypeFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $formTypeFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooType',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type',
                    'getExtension' => 'php',
                    'getContents'  => FormData::FOO_FORM_TYPE,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type' .
                        DIRECTORY_SEPARATOR . 'FooType.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $formTypeFileMock;
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getExceptionFileMock()
    {
        $exceptionFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $exceptionFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'InvalidFormException',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Exception',
                    'getExtension' => 'php',
                    'getContents'  => FormData::FORM_EXCEPTION,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR . 'InvalidFormException.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $exceptionFileMock;
    }
}
