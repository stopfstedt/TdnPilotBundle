<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\Symfony\ServiceFileUtil;

/**
 * Abstract Class AbstractServiceManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractServiceManipulator extends AbstractManipulator implements ServiceManipulatorInterface
{
    /**
     * @var ServiceFileUtil
     */
    private $serviceFileUtil;

    /**
     * @param ServiceFileUtil $serviceFileUtil
     */
    public function setServiceFileUtil(ServiceFileUtil $serviceFileUtil)
    {
        $this->serviceFileUtil = $serviceFileUtil;
    }

    /**
     * @return ServiceFileUtil
     */
    public function getServiceFileUtil()
    {
        return $this->serviceFileUtil;
    }
}
