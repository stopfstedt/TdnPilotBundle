<?php

namespace Tdn\PilotBundle\Model;

/**
 * Interface FileInterface
 * @package Tdn\PilotBundle\Model
 */
interface FileInterface
{
    /**
     * @param string $contents
     * @return $this
     */
    public function setContents($contents);

    /**
     * @return string
     */
    public function getContents();

    /**
     * @param  bool $auxFile
     */
    public function setAuxFile($auxFile);

    /**
     * @return bool
     */
    public function isAuxFile();

    /**
     * @param bool $serviceFile
     */
    public function setServiceFile($serviceFile);

    /**
     * @return bool
     */
    public function isServiceFile();
}
