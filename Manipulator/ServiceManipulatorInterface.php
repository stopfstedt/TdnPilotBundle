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
     * @param string $format
     */
    public function setFormat($format);

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @param bool $updatingDiFile
     */
    public function setUpdatingDiConfFile($updatingDiFile);

    /**
     * @return bool
     */
    public function isUpdatingDiConfFile();
}
