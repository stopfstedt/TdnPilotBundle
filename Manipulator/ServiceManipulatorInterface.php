<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\Utils\DiUtils;

/**
 * Interface ServiceManipulatorInterface
 * @package Tdn\PilotBundle\Manipulator
 */
interface ServiceManipulatorInterface extends ManipulatorInterface
{
    /**
     * @param DiUtils $diUtils
     */
    public function setDiUtils(DiUtils $diUtils);

    /**
     * @return DiUtils
     */
    public function getDiUtils();

    /**
     * @param bool $updatingDiFile
     */
    public function setUpdatingDiConfFile($updatingDiFile);

    /**
     * @return bool
     */
    public function isUpdatingDiConfFile();
}
