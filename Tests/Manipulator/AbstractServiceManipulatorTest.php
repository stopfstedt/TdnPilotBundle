<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Tdn\PilotBundle\Manipulator\AbstractServiceManipulator;
use Tdn\PilotBundle\Manipulator\ServiceManipulatorInterface;
use Tdn\PilotBundle\Services\Utils\DependencyInjection\DiXmlManipulator;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use \Mockery;

/**
 * Class AbstractServiceManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
abstract class AbstractServiceManipulatorTest extends AbstractManipulatorTest
{
    /**
     * @return ServiceManipulatorInterface
     */
    protected function getServiceManipulator()
    {
        $manipulator = $this->getManipulator();
        if (!$manipulator instanceof AbstractServiceManipulator) {
            throw new \RuntimeException(
                sprintf(
                    'Expected instance of %s, %s given.',
                    get_class($manipulator),
                    'AbstractServiceManipulator'
                )
            );
        }

        return $manipulator;
    }

    /**
     * @return string
     */
    protected function getExtensionFile()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'foobar.extension.out'
        ;
    }

    public function testUpdatingDiConfFile()
    {
        $manipulator = $this->getServiceManipulator();
        $this->assertTrue($manipulator->isUpdatingDiConfFile());
        $manipulator->setUpdatingDiConfFile(false);
        $this->assertFalse($manipulator->isUpdatingDiConfFile());
    }

    public function testExtensionFile()
    {
        $serviceManipulator = $this->getServiceManipulator();
        $serviceManipulator->setExtensionFile($this->getExtensionFile());
        $this->assertEquals($this->getExtensionFile(), $serviceManipulator->getExtensionFile());
    }

    public function testDiManipulator()
    {
        $serviceManipulator = $this->getServiceManipulator();
        $diManipulator = new DiXmlManipulator();
        $serviceManipulator->setDiManipulator($diManipulator);
        $this->assertEquals($diManipulator, $serviceManipulator->getDiManipulator());
    }

    public function testBasicXmlServiceFile()
    {
        $serviceManipulator = $this->getServiceManipulator();
        $serviceManipulator->setXmlServiceFile();
        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            'basic.service.xml.out'
        );

        $this->assertEquals(simplexml_load_string($content), $serviceManipulator->getXmlServiceFile());
    }

    public function testGetPopulatedXmlServiceFile()
    {
        $serviceFileName = 'foo.service.xml.out';
        $serviceManipulator = $this->getServiceManipulator();
        $serviceManipulator->setXmlServiceFile($this->getXmlServiceFileMock($serviceFileName));

        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            $serviceFileName
        );

        $this->assertEquals(simplexml_load_string($content), $serviceManipulator->getXmlServiceFile());
    }

    /**
     * @param string $fileName
     *
     * @return GeneratedFileInterface
     */
    public function getXmlServiceFileMock($fileName)
    {
        $content = @file_get_contents(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR .
            $fileName
        );

        $xmlFile = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFileInterface');
        $xmlFile
            ->shouldReceive('getContents')
            ->andReturn($content)
            ->zeroOrMoreTimes()
        ;

        return $xmlFile;
    }
}
