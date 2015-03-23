<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

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
        $manipulator = new ManagerManipulator(
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
        return new ArrayCollection();
    }

    protected function getExpectedMessages()
    {
        return new ArrayCollection([
            sprintf(
                'Make sure to load "%s" in the %s file to enable the new services.',
                'managers.xml',
                $this->getDefaultDiFile()
            )
        ]);
    }

    /**
     * @return GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $managerFileMock      = $this->getManagerFileMock();
        $mgrInterfaceFileMock = $this->getMgrInterfaceFileMock();
        $mgrServiceMock       = $this->getManagerServiceMock();

        return [
            $managerFileMock->getFullPath()      => $managerFileMock,
            $mgrInterfaceFileMock->getFullPath() => $mgrInterfaceFileMock,
            $mgrServiceMock->getFullPath()       => $mgrServiceMock
        ];
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getManagerFileMock()
    {
        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'manager.out'
        );

        $managerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $managerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooManager',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getContents'  => $content,
                    'getFullPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManager.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $managerFileMock;
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getMgrInterfaceFileMock()
    {
        $interfaceContent = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'manager.interface.data'
        );

        $mgrInterfaceFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $mgrInterfaceFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooManagerInterface',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getContents'  => $interfaceContent,
                    'getFullPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManagerInterface.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $mgrInterfaceFileMock;
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getManagerServiceMock()
    {
        $mgrServiceContent = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'managers.service.xml.out'
        );

        $mgrServiceMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $mgrServiceMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'managers',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getExtension' => 'xml',
                    'getContents'  => $mgrServiceContent,
                    'getFullPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }
}
