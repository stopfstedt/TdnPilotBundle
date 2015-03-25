<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;
use Tdn\PilotBundle\Model\File;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\ManagerData;

/**
 * Class ManagerManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
class ManagerManipulatorTest extends AbstractServiceManipulatorTest
{
    /**
     * @return ManagerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = new ManagerManipulator();

        $manipulator->setTemplateStrategy($this->getTemplateStrategy());
        $manipulator->setBundle($this->getBundle());
        $manipulator->setMetadata($this->getMetadata());
        $manipulator->setOverwrite(false);
        $manipulator->setTargetDirectory($this->getOutDir());
        $manipulator->setServiceFileUtils($this->getServiceUtils());
        $manipulator->setFormat('xml');

        return $manipulator->prepare();
    }

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    protected function getFileDependencies()
    {
        return new ArrayCollection();
    }

    protected function getExpectedMessages()
    {
        return new ArrayCollection([
            sprintf(
                'Make sure to load "%s" in your extension file to enable the new services.',
                'managers.xml'
            )
        ]);
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
                    'getFilename'  => 'FooManager',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManager.php'
                ]
            )
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
                    'getFilename'  => 'FooManagerInterface',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManagerInterface.php'
                ]
            )
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
                    'getFilename'  => 'managers',
                    'getExtension' => 'xml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }
}
