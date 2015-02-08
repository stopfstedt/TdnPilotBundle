<?php

namespace Tdn\PilotBundle\Model;

interface GeneratedFileInterface
{
    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getFullPath();

    /**
     * @param string $extension
     * @return $this
     */
    public function setExtension($extension);

    /**
     * @return string
     */
    public function getExtension();

    /**
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename);

    /**
     * @return string
     */
    public function getFilename();

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
     * @param  bool $forceNew
     * @return $this
     */
    public function setForceNew($forceNew);

    /**
     * @return bool
     */
    public function hasForceNew();

    /**
     * @return bool
     */
    public function isValid();
}
