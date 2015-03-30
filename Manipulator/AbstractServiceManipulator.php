<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PilotBundle\Services\Utils\DiUtils;

/**
 * Abstract Class AbstractServiceManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractServiceManipulator extends AbstractManipulator implements ServiceManipulatorInterface
{
    /**
     * @var DiUtils
     */
    private $diUtils;

    /**
     * @var string
     */
    private $format;

    /**
     * @var bool
     */
    private $updatingDiFile;

    /**
     * @param DiUtils $diUtils
     */
    public function setDiUtils(DiUtils $diUtils)
    {
        $this->diUtils = $diUtils;
    }

    /**
     * @return DiUtils
     */
    public function getDiUtils()
    {
        return $this->diUtils;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
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

    /**
     * @return string
     */
    protected function getDefaultExtensionFile()
    {
        return sprintf(
            '%s/DependencyInjection/%s.php',
            $this->getBundle()->getPath(),
            str_replace('Bundle', 'Extension', $this->getBundle()->getName())
        );
    }
}
