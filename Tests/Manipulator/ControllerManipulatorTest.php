<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use Tdn\PilotBundle\Model\File;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\ControllerData;

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
        $this->assertEquals('v1', $manipulator->getRouteNamePrefix());
    }

    public function testResource()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals(true, $manipulator->isResource());
        $manipulator->setResource(false);
        $this->assertEquals(false, $manipulator->isResource());
    }

    public function testSwagger()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals(true, $manipulator->hasSwagger());
        $manipulator->setSwagger(false);
        $this->assertEquals(false, $manipulator->hasSwagger());
    }

    public function testGenerateTests()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals(false, $manipulator->shouldGenerateTests());
        $manipulator->setGenerateTests(true);
        $this->assertEquals(true, $manipulator->shouldGenerateTests());
    }

    /**
     * @return ControllerManipulator
     */
    protected function getManipulator()
    {
        $manipulator = new ControllerManipulator();

        $manipulator->setTemplateStrategy($this->getTemplateStrategy());
        $manipulator->setBundle($this->getBundle());
        $manipulator->setMetadata($this->getMetadata());
        $manipulator->setSwagger(true);
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
            new File($handlerFile)
        ]);
    }

    /**
     * @return File[]
     */
    protected function getGeneratedFiles()
    {
        $controllerFileMock = $this->getControllerFileMock();

        return [
            $controllerFileMock->getRealPath() => $controllerFileMock
        ];
    }

    /**
     * @return File
     */
    protected function getControllerFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => ControllerData::BASIC_FOO_CONTROLLER,
                    'getFilename'  => 'FooController',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooController.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }
}
