<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\Symfony\ServiceFileUtil;

/**
 * Interface ServiceManipulatorInterface
 * @package Tdn\PilotBundle\Manipulator
 */
interface ServiceManipulatorInterface extends ManipulatorInterface
{
    /**
     * @param ServiceFileUtil $serviceFileUtil
     */
    public function setServiceFileUtil(ServiceFileUtil $serviceFileUtil);

    /**
     * @return ServiceFileUtil
     */
    public function getServiceFileUtil();
}
