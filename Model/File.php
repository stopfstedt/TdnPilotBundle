<?php

namespace Tdn\PilotBundle\Model;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class File
 * @package Tdn\PilotBundle\Model
 */
class File extends SplFileInfo
{
    private $fileLocation;

    /**
     * @var string
     */
    private $filteredContents;

    /**
     * @var bool
     */
    private $auxFile;

    /**
     * @var bool
     */
    private $serviceFile;

    /**
     * @param string $file
     * @param null $relativePath
     * @param null $relativePathName
     */
    public function __construct($file, $relativePath = null, $relativePathName = null)
    {
        $this->fileLocation = $file;
        $this->auxFile = false;
        $this->serviceFile = false;

        parent::__construct($file, $relativePath, $relativePathName);
    }

    /**
     * @param string $filteredContents
     *
     * @return $this
     */
    public function setFilteredContents($filteredContents)
    {
        $this->filteredContents = $filteredContents;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilteredContents()
    {
        return $this->filteredContents;
    }

    /**
     * @param  bool $auxFile
     * @return $this
     */
    public function setAuxFile($auxFile)
    {
        $this->auxFile = $auxFile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAuxFile()
    {
        return $this->auxFile;
    }

    /**
     * @param bool $serviceFile
     */
    public function setServiceFile($serviceFile)
    {
        $this->serviceFile = $serviceFile;
    }

    /**
     * @return bool
     */
    public function isServiceFile()
    {
        return $this->serviceFile;
    }

    /**
     * @return string
     */
    public function getRealPath()
    {
        return (parent::getRealPath()) ?: $this->fileLocation;
    }
}
