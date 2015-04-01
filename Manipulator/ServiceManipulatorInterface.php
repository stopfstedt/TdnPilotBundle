<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\DependencyInjection\ServiceUtils;

/**
 * Interface ServiceManipulatorInterface
 * @package Tdn\PilotBundle\Manipulator
 */
interface ServiceManipulatorInterface extends ManipulatorInterface
{
    /**
     * @param ServiceUtils $diUtils
     */
    public function setServiceUtils(ServiceUtils $diUtils);

    /**
     * @return ServiceUtils
     */
    public function getServiceUtils();

    /**
     * @param bool $updatingDiFile
     */
    public function setUpdatingDiConfFile($updatingDiFile);

    /**
     * @return bool
     */
    public function isUpdatingDiConfFile();
}
