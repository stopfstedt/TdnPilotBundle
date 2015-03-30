<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Tuck\ConverterBundle\ConfigFormatConverter;
use Tuck\ConverterBundle\Dumper\StandardDumperFactory;
use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\Loader\StandardLoaderFactory;
use Tdn\PilotBundle\Manipulator\AbstractServiceManipulator;
use Tdn\PilotBundle\Manipulator\ServiceManipulatorInterface;
use Tdn\PilotBundle\Services\Utils\DiUtils;
use \Mockery;

/**
 * Class AbstractServiceManipulatorTest
 * @package Tdn\PilotBundle\Tests\Manipulator
 */
abstract class AbstractServiceManipulatorTest extends AbstractManipulatorTest
{
   /**
     * @param string $format
     *
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

    public function testDiUtils()
    {
        /** @var ServiceManipulatorInterface $manipulator */
        $manipulator = $this->getServiceManipulator()->reset();
        $this->assertNull($manipulator->getDiUtils());
        $manipulator->setDiUtils($this->getDiUtils());
        $this->assertEquals($this->getDiUtils(), $manipulator->getDiUtils());
    }

    public function testUpdatingDiConfFile()
    {
        $manipulator = $this->getServiceManipulator();
        $this->assertTrue($manipulator->isUpdatingDiConfFile());
        $manipulator->setUpdatingDiConfFile(false);
        $this->assertFalse($manipulator->isUpdatingDiConfFile());
    }

    protected function getDefaultDiFile()
    {
        return sprintf(
            '%s/DependencyInjection/%s.php',
            $this->getBundle()->getPath(),
            str_replace('Bundle', 'Extension', $this->getBundle()->getName())
        );
    }

    /**
     * @return DiUtils
     */
    protected function getDiUtils()
    {
        return new DiUtils(new ConfigFormatConverter(
            new StandardLoaderFactory(),
            new StandardDumperFactory(),
            new SysTempFileFactory()
        ));
    }
}
