<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\HandlerData;

/**
 * Class HandlerManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
class HandlerManipulatorTest extends AbstractServiceManipulatorTest
{
    /**
     * @return HandlerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = new HandlerManipulator();

        $manipulator->setTemplateStrategy($this->getTemplateStrategy());
        $manipulator->setBundle($this->getBundle());
        $manipulator->setMetadata($this->getMetadata());
        $manipulator->setOverwrite(false);
        $manipulator->setTargetDirectory($this->getOutDir());
        $manipulator->setDiUtils($this->getDiUtils());
        $manipulator->setFormat('xml');

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

        $formType = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Form' . DIRECTORY_SEPARATOR .
            'Type' . DIRECTORY_SEPARATOR . '%sType.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new SplFileInfo($managerFile, null, null),
            new SplFileInfo($formType, null, null)
        ]);
    }

    /**
     * @return GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $handlerFileMock    = $this->getHandlerFileMock();
        $handlerServiceMock = $this->getHandlerServiceMock();

        return [
            $handlerFileMock->getFullPath()    => $handlerFileMock,
            $handlerServiceMock->getFullPath() => $handlerServiceMock
        ];
    }

    protected function getExpectedMessages()
    {
        return new ArrayCollection([
            sprintf(
                'Make sure to load "%s" in the %s file to enable the new services.',
                'handlers.xml',
                $this->getDefaultDiFile()
            )
        ]);
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getHandlerFileMock()
    {
        $handlerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $handlerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooHandler',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Handler',
                    'getExtension' => 'php',
                    'getContents'  => HandlerData::FOO_HANDLER,
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
        $handlrServMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'handlers',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getExtension' => 'xml',
                    'getContents'  => HandlerData::FOO_HANDLER_SERVICE_XML,
                    'getFullPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }
}
