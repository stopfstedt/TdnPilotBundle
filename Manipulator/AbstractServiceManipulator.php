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
    private $serviceFileUtil;

    /**
     * @param ServiceFileUtils $serviceFileUtil
     */
    public function setServiceFileUtil(ServiceFileUtils $serviceFileUtil)
    {
        $this->serviceFileUtil = $serviceFileUtil;
    }

    /**
     * @return ServiceFileUtils
     */
    public function getServiceFileUtil()
    {
        return $this->serviceFileUtil;
    }
}
