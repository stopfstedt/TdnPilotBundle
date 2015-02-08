<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\RoutingManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

/**
 * Class RoutingManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
class RoutingManipulatorTest extends AbstractManipulatorTest
{
    public function testRoutingFile()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals('routing.yml', $manipulator->getRoutingFile());
        $manipulator->setRoutingFile('test.yml');
        $this->assertEquals('test.yml', $manipulator->getRoutingFile());
    }

    public function testRoutePrefix()
    {
        $manipulator = $this->getManipulator();
        $this->assertEquals('v1', $manipulator->getRoutePrefix());
        $manipulator->setRoutePrefix('v1.5');
        $this->assertEquals('v1.5', $manipulator->getRoutePrefix());
    }

    public function testRemove()
    {
        $manipulator = $this->getManipulator();
        $this->assertFalse($manipulator->shouldRemove());
        $manipulator->setRemove(true);
        $this->assertTrue($manipulator->shouldRemove());
    }

    /**
     * @return RoutingManipulator
     */
    protected function getManipulator()
    {
        $manipulator = new RoutingManipulator(
            $this->getTemplateStrategy(),
            $this->getBundle(),
            $this->getMetadata()
        );

        $manipulator->setRoutePrefix('v1');
        $manipulator->setRoutingFile('routing.yml');
        $manipulator->setOverwrite(false);
        $manipulator->setTargetDirectory($this->getOutDir());

        return $manipulator->prepare();
    }

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    protected function getFileDependencies()
    {
        $controllerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '%sController.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new SplFileInfo($controllerFile, null, null)
        ]);
    }

    /**
     * @return ArrayCollection|GeneratedFileInterface[]
     */
    protected function getGeneratedFiles()
    {
        $routingFileMock = $this->getRoutingFileMock();

        return [
            $routingFileMock->getFullPath() => $routingFileMock
        ];
    }

    /**
     * @return GeneratedFileInterface
     */
    protected function getRoutingFileMock()
    {
        $routingFileContents = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'routing.out'
        );

        $routingFileMock = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'routing',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getExtension' => 'yml',
                    'getContents'  => $routingFileContents,
                    'getFullPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routing.yml',
                    'hasForceNew'  => true
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $routingFileMock;
    }
}
