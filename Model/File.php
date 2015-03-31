<?php

namespace Tdn\PilotBundle\Model;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class File
 * @package Tdn\PilotBundle\Model
 */
class File extends SplFileInfo implements FileInterface
{
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

    /**
     * @param string $file
     * @param null $relativePath
     * @param null $relativePathName
     */
    public function __construct($file, $relativePath = null, $relativePathName = null)
    {
        $this->auxFile = false;
        $this->serviceFile = false;

        parent::__construct($file, $relativePath, $relativePathName);
    }

    /**
     * @param string $contents
     *
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

    public function save()
    {
        if ($this->isWritable()) {
            $file = $this->openFile('w');
            $file->fwrite($this->getContents());
            $file = null;
        }

        throw new IOException(
            sprintf(
                'Could not write to file %s',
                $this->getRealPath()
            )
        );
    }
}
