<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

/**
 * Class ControllerManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
class ControllerManipulatorTest extends AbstractManipulatorTest
{
    public function testRoutePrefix()
    {
        $manipulator = $this->getManipulator();
        $manipulator->setRoutePrefix('v1');
        $this->assertEquals('v1', $manipulator->getRoutePrefix());
    }

    public function testRouteNamePrefix()
    {
        $manipulator = $this->getManipulator();
        $manipulator->setRoutePrefix('v1');
        $this->assertEquals('v1', $manipulator->getRouteNamePrefix());

    }

    public function testResource()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals(true, $manipulator->isResource());
        $manipulator->setResource(false);
        $this->assertEquals(false, $manipulator->isResource());
    }

    public function testDocument()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals(true, $manipulator->hasDocument());
        $manipulator->setDocument(false);
        $this->assertEquals(false, $manipulator->hasDocument());
    }

    public function testGenerateTests()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals(false, $manipulator->hasGenerateTests());
        $manipulator->setGenerateTests(true);
        $this->assertEquals(true, $manipulator->hasGenerateTests());
    }

    /**
     * @return ControllerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = new ControllerManipulator(
            $this->getOutputEngine(),
            $this->getBundle(),
            $this->getMetadata()
        );
        $manipulator->setDocument(true);
        $manipulator->setResource(true);
        $manipulator->setOverwrite(false);
        $manipulator->setTargetDirectory($this->getOutDir());

        return $manipulator->prepare();
    }

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    protected function getFileDependencies()
    {
        $handlerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . '%sHandler.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new SplFileInfo($handlerFile, null, null)
        ]);
    }

    /**
     * @return GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $controllerFileMock = $this->getControllerFileMock();

        return [
            $controllerFileMock->getFullPath() => $controllerFileMock
        ];
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getControllerFileMock()
    {
        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'controller.out'
        );

        $controllerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'FooController',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
                    'getExtension' => 'php',
                    'getContents'  => $content,
                    'getFullPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooController.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }
}
