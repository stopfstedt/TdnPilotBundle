<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Manipulator\RoutingManipulator;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Model\FileInterface;
use \Mockery;
use Tdn\PilotBundle\Tests\Fixtures\RoutingData;

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
        $manipulator = new RoutingManipulator();

        $manipulator->setTemplateStrategy($this->getTemplateStrategy());
        $manipulator->setBundle($this->getBundle());
        $manipulator->setMetadata($this->getMetadata());
        $manipulator->setRoutePrefix('v1');
        $manipulator->setRoutingFile('routing.yml');
        $manipulator->setOverwrite(false);
        $manipulator->setTargetDirectory($this->getOutDir());

        return $manipulator->prepare();
    }

    /**
     * @return ArrayCollection|File[]
     */
    protected function getFileDependencies()
    {
        $controllerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '%sController.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new File($controllerFile, null, null)
        ]);
    }

    /**
     * @return File[]
     */
    protected function getGeneratedFiles()
    {
        $routingFileMock = $this->getRoutingFileMock();

        return [
            $routingFileMock->getRealPath() => $routingFileMock
        ];
    }

    /**
     * @return File
     */
    protected function getRoutingFileMock()
    {
        $routingFileMock = Mockery::mock('\Tdn\PilotBundle\Model\File');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilteredContents'  => RoutingData::ROUTING_FILE,
                    'getFilename'  => 'routing',
                    'getExtension' => 'yaml',
                    'isAuxFile'    => true,
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routing.yml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $routingFileMock;
    }
}
