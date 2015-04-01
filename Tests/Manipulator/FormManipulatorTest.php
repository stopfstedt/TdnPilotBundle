<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\FormManipulator;
use Tdn\PilotBundle\Model\FileInterface;
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
     * @return FileInterface[]
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
     * @return FileInterface
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
                    'getFilteredContents'  => FormData::FOO_FORM_TYPE,
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
     * @return FileInterface
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
                    'getFilteredContents'  => FormData::FORM_EXCEPTION,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR . 'InvalidFormException.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $exceptionFileMock;
    }
}
