<?php

namespace Tdn\PilotBundle\Model;

/**
 * Class GeneratedFile
 * @package Tdn\PilotBundle\Model
 */
class GeneratedFile implements GeneratedFileInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var string
     */
    private $contents;

    /**
     * @var bool
     */
    private $auxFile;

    /**
     * @var bool
     */
    private $serviceFile;

    public function __construct()
    {
        $this->auxFile = false;
        $this->serviceFile = false;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        try {
            if ($this->isValid()) {
                return $this->getPath() . DIRECTORY_SEPARATOR . $this->getFilename() . '.' . $this->getExtension();
            }
        } catch (\RuntimeException $e) {
            // Do nothing
        }

        return '';
    }

    /**
     * @param string $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $contents
     * @return $this
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Gets the content of the file constructed from full path.
     *
     * Will return contents in this order: if content is set already
     * return that. Otherwise try to get the fullpath's content
     * If that fails, value will be null.
     *
     * @return string
     */
    public function getContents()
    {
        try {
            if (null === $this->contents && is_file($this->getFullPath())) {
                try {
                    $this->contents = file_get_contents($this->getFullPath());
                } catch (\Exception $e) {
                    // do nothing... file is non-existent
                }
            }
        } catch (\RuntimeException $e) {
            // full path is not valid.
        }

        return $this->contents;
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
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function isValid()
    {
        if (null === $this->getPath()) {
            throw new \RuntimeException('Path is not set. Please set the path.');
        }

        if (null === $this->getFilename()) {
            throw new \RuntimeException('File name is not set. Please set the file name.');
        }

        if (null === $this->getExtension()) {
            throw new \RuntimeException('Extension is not set. Please set the extension.');
        }

        return true;
    }
}
