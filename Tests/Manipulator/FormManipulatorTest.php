<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PilotBundle\Manipulator\FormManipulator;
use Tdn\PilotBundle\Model\File;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\FormData;

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
        $manipulator = new FormManipulator();

        $manipulator->setTemplateStrategy($this->getTemplateStrategy());
        $manipulator->setBundle($this->getBundle());
        $manipulator->setMetadata($this->getMetadata());
        $manipulator->setOverwrite(false);
        $manipulator->setTargetDirectory($this->getOutDir());

        return $manipulator->prepare();
    }

    /**
     * @return ArrayCollection|File[]
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
            new File($managerFile)
        ]);
    }

    /**
     * @return File[]
     */
    protected function getGeneratedFiles()
    {
        $formTypeFileMock  = $this->getFormTypeMock();
        $exceptionFileMock = $this->getExceptionFileMock();

        return [
            $formTypeFileMock->getRealPath()  => $formTypeFileMock,
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
                    'getFilteredContents'  => FormData::FOO_FORM_TYPE,
                    'getFilename'  => 'FooType',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type',
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Form' .
                        DIRECTORY_SEPARATOR . 'Type' .
                        DIRECTORY_SEPARATOR . 'FooType.php'
                ]
            )
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
                    'getFilename'  => 'InvalidFormException',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Exception',
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Exception' .
                        DIRECTORY_SEPARATOR . 'InvalidFormException.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $exceptionFileMock;
    }
}
