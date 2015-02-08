<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\FormManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

/**
 * Class FormManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
class FormManipulatorTest extends AbstractManipulatorTest
{
    /**
     * @return FormManipulator
     */
    protected function getManipulator()
    {
        $manipulator = new FormManipulator(
            $this->getTemplateStrategy(),
            $this->getBundle(),
            $this->getMetadata()
        );

        $manipulator->setOverwrite(false);
        $manipulator->setTargetDirectory($this->getOutDir());

        return $manipulator->prepare();
    }

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    protected function getFileDependencies()
    {
        $managerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Entity' . DIRECTORY_SEPARATOR .
            'Manager' . DIRECTORY_SEPARATOR . '%sManager.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new SplFileInfo($managerFile, null, null)
        ]);
    }

    /**
     * @return GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $formTypeFileMock  = $this->getFormTypeMock();
        $exceptionFileMock = $this->getExceptionFileMock();

        return [
            $formTypeFileMock->getFullPath()  => $formTypeFileMock,
            $exceptionFileMock->getFullPath() => $exceptionFileMock
        ];
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getFormTypeMock()
    {
        $typeContent = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'form.type.out'
        );

        $formTypeFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $formTypeFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooType',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type',
                    'getExtension' => 'php',
                    'getContents'  => $typeContent,
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
        $exceptionContent = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'form.exception.out'
        );

        $exceptionFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $exceptionFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'InvalidFormException',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Exception',
                    'getExtension' => 'php',
                    'getContents'  => $exceptionContent,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR . 'InvalidFormException.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $exceptionFileMock;
    }
}
