<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\DependencyInjection\ServiceUtils;

/**
 * Abstract Class AbstractServiceManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractServiceManipulator extends AbstractManipulator implements ServiceManipulatorInterface
{
    /**
     * @var ServiceUtils
     */
    private $serviceUtils;

    /**
     * @var bool
     */
    private $updatingDiFile;

    /**
     * @param ServiceUtils $serviceUtils
     */
    public function setServiceUtils(ServiceUtils $serviceUtils)
    {
        $this->serviceUtils = $serviceUtils;
    }

    /**
     * @return ServiceUtils
     */
    public function getServiceUtils()
    {
        return $this->serviceUtils;
    }

    /**
     * @param bool $updatingDiFile
     */
    public function setUpdatingDiConfFile($updatingDiFile)
    {
        $this->updatingDiFile = $updatingDiFile;
    }

    /**
     * @return bool
     */
    public function isUpdatingDiConfFile()
    {
        return $this->updatingDiFile;
    }
}
