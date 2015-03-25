<?php

namespace Tdn\PilotBundle\Tests\Manipulator;

use Tdn\PilotBundle\Manipulator\AbstractServiceManipulator;
use Tdn\PilotBundle\Manipulator\ServiceManipulatorInterface;
use Tdn\PilotBundle\Services\Utils\Symfony\ServiceFileUtils;
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
                    'AbstractServiceManipulator',
                    get_class($manipulator)
                )
            );
        }

        return $manipulator;
    }

    public function testServiceUtils()
    {
        /** @var ServiceManipulatorInterface $manipulator */
        $manipulator = $this->getServiceManipulator();
        $this->assertNotNull($manipulator->getServiceFileUtils());
        $manipulator->setServiceFileUtils($this->getServiceUtils());
        $this->assertEquals($this->getServiceUtils(), $manipulator->getServiceFileUtils());
    }

    /**
     * @return ServiceFileUtils
     */
    protected function getServiceUtils()
    {
        return new ServiceFileUtils();
    }
}
