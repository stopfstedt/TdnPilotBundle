<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\Services\Utils\DiXmlUtils;

/**
 * Interface ServiceManipulatorInterface
 * @package Tdn\PilotBundle\Manipulator
 */
interface ServiceManipulatorInterface extends ManipulatorInterface
{
    /**
     * @param DiXmlUtils $diManipulator
     */
    public function setDiUtils(DiXmlUtils $diManipulator);

    /**
     * @return DiXmlUtils
     */
    public function getDiUtils();

    /**
     * @param GeneratedFileInterface|null $file
     */
    public function setXmlServiceFile(GeneratedFileInterface $file = null);

    /**
     * @return \SimpleXMLElement
     */
    public function getXmlServiceFile();

    /**
     * @param bool $updatingDiFile
     */
    public function setUpdatingDiConfFile($updatingDiFile);

    /**
     * @return bool
     */
    public function isUpdatingDiConfFile();
}
