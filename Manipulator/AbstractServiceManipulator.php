<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\Utils\Symfony\ServiceFileUtils;

/**
 * Abstract Class AbstractServiceManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractServiceManipulator extends AbstractManipulator implements ServiceManipulatorInterface
{
    /**
     * @var ServiceFileUtils
     */
    private $serviceFileUtils;

    /**
     * @param ServiceFileUtils $serviceFileUtils
     */
    public function setServiceFileUtils(ServiceFileUtils $serviceFileUtils)
    {
        $this->serviceFileUtils = $serviceFileUtils;
    }

    /**
     * @return ServiceFileUtils
     */
    public function getServiceFileUtils()
    {
        return $this->serviceFileUtils;
    }
}
