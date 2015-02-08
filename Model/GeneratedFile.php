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
    private $forceNew;

    public function __construct()
    {
        $this->forceNew = false;
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
     * @param  bool $forceNew
     * @return $this
     */
    public function setForceNew($forceNew)
    {
        $this->forceNew = $forceNew;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasForceNew()
    {
        return $this->forceNew;
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
