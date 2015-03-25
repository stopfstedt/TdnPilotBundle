<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;
use Tdn\PilotBundle\Model\File;
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
        $manipulator->setServiceFileUtils($this->getServiceUtils());
        $manipulator->setFormat('xml');

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

        $formType = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Form' . DIRECTORY_SEPARATOR .
            'Type' . DIRECTORY_SEPARATOR . '%sType.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new File($managerFile),
            new File($formType)
        ]);
    }

    /**
     * @return File[]
     */
    protected function getGeneratedFiles()
    {
        $handlerFileMock    = $this->getHandlerFileMock();
        $handlerServiceMock = $this->getHandlerServiceMock();

        return [
            $handlerFileMock->getRealPath()    => $handlerFileMock,
            $handlerServiceMock->getRealPath() => $handlerServiceMock
        ];
    }

    /**
     * @return ArrayCollection
     */
    protected function getExpectedMessages()
    {
        return new ArrayCollection([
            sprintf(
                'Make sure to load "%s" in your extension file to enable the new services.',
                'handlers.xml'
            )
        ]);
    }

    /**
     * @return File
     */
    protected function getHandlerFileMock()
    {
        $handlerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $handlerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => HandlerData::FOO_HANDLER,
                    'getFilename'  => 'FooHandler',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Handler',
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . 'FooHandler.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlerFileMock;
    }

    /**
     * @return File
     */
    protected function getHandlerServiceMock()
    {
        $handlrServMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => HandlerData::FOO_HANDLER_SERVICE_XML,
                    'getFilename'  => 'handlers',
                    'getExtension' => 'xml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }
}
