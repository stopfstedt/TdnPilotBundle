<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\Services\Utils\DependencyInjection\DiXmlManipulator;

/**
 * Interface ServiceManipulatorInterface
 * @package Tdn\PilotBundle\Manipulator
 */
interface ServiceManipulatorInterface extends ManipulatorInterface
{
    /**
     * @param string|null $extensionFile
     */
    public function setExtensionFile($extensionFile = null);

    /**
     * @return string
     */
    public function getExtensionFile();

    /**
     * @param DiXmlManipulator $diManipulator
     */
    public function setDiManipulator(DiXmlManipulator $diManipulator);

    /**
     * @return DiXmlManipulator
     */
    public function getDiManipulator();

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
