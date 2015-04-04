<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\Utils\Symfony\ServiceFileUtils;

/**
 * Interface ServiceManipulatorInterface
 * @package Tdn\PilotBundle\Manipulator
 */
interface ServiceManipulatorInterface extends ManipulatorInterface
{
    /**
     * @param ServiceFileUtils $serviceFileUtil
     */
    public function setServiceFileUtils(ServiceFileUtils $serviceFileUtil);

    /**
     * @return ServiceFileUtils
     */
    public function getServiceFileUtils();
}
