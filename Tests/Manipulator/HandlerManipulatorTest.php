<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

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
        $manipulator = new HandlerManipulator(
            $this->getOutputEngine(),
            $this->getBundle(),
            $this->getMetadata()
        );

        $manipulator->setOverwrite(false);
        $manipulator->setExtensionFile($this->getExtensionFile());
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
