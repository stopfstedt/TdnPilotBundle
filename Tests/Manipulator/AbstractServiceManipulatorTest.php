<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Tuck\ConverterBundle\ConfigFormatConverter;
use Tuck\ConverterBundle\Dumper\StandardDumperFactory;
use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\Loader\StandardLoaderFactory;
use Tdn\PilotBundle\Manipulator\AbstractServiceManipulator;
use Tdn\PilotBundle\Manipulator\ServiceManipulatorInterface;
use Tdn\PilotBundle\Services\DependencyInjection\ServiceUtils;
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
        $this->assertNull($manipulator->getServiceFileUtils());
        $manipulator->setServiceFileUtils($this->getServiceUtils());
        $this->assertEquals($this->getServiceUtils(), $manipulator->getServiceFileUtils());
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
            '%s/Symfony/%s.php',
            $this->getBundle()->getPath(),
            str_replace('Bundle', 'Extension', $this->getBundle()->getName())
        );
    }

    /**
     * @return ServiceUtils
     */
    protected function getServiceUtils()
    {
        return new ServiceUtils(new ConfigFormatConverter(
            new StandardLoaderFactory(),
            new StandardDumperFactory(),
            new SysTempFileFactory()
        ));
    }
}
