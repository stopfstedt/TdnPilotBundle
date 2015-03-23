<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Tdn\PilotBundle\Manipulator\AbstractServiceManipulator;
use Tdn\PilotBundle\Manipulator\ServiceManipulatorInterface;
use Tdn\PilotBundle\Services\Utils\DiXmlUtils;
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

    public function testUpdatingDiConfFile()
    {
        $manipulator = $this->getServiceManipulator();
        $this->assertTrue($manipulator->isUpdatingDiConfFile());
        $manipulator->setUpdatingDiConfFile(false);
        $this->assertFalse($manipulator->isUpdatingDiConfFile());
    }

    public function testDiUtils()
    {
        $serviceManipulator = $this->getServiceManipulator();
        $diManipulator = new DiXmlUtils();
        $serviceManipulator->setDiUtils($diManipulator);
        $this->assertEquals($diManipulator, $serviceManipulator->getDiUtils());
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

    protected function getDefaultDiFile()
    {
        return sprintf(
            '%s/DependencyInjection/%s.php',
            $this->getBundle()->getPath(),
            str_replace('Bundle', 'Extension', $this->getBundle()->getName())
        );
    }
}
