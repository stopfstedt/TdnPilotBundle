<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Interface ManipulatorInterface
 * @package Tdn\PilotBundle\Manipulator
 */
interface ManipulatorInterface
{
    /**
     * @param TemplateStrategyInterface $templateStrategy
     */
    public function setTemplateStrategy(TemplateStrategyInterface $templateStrategy);

    /**
     * @return TemplateStrategyInterface
     */
    public function getTemplateStrategy();

    /**
     * @param BundleInterface $bundle
     */
    public function setBundle(BundleInterface $bundle);

    /**
     * @return BundleInterface
     */
    public function getBundle();

    /**
     * @return string
     */
    public function getEntity();

    /**
     * @return string
     */
    public function getEntityNamespace();

    /**
     * @param ClassMetadata $metadata
     */
    public function setMetadata(ClassMetadata $metadata);

    /**
     * @return ClassMetadata
     */
    public function getMetadata();

    /**
     * @param Collection $generatedFiles
     */
    public function setFiles(Collection $generatedFiles);

    /**
     * @param File $generatedFile
     */
    public function addFile(File $generatedFile);

    /**
     * @return ArrayCollection|File[]
     */
    public function getFiles();

    /**
     * @param Collection $fileDependencies
     */
    public function setFileDependencies(Collection $fileDependencies);

    /**
     * @param File $fileDependency
     */
    public function addFileDependency(File $fileDependency);

    /**
     * @return ArrayCollection|File[]
     */
    public function getFileDependencies();

    /**
     * @param Collection $messages
     */
    public function setMessages(Collection $messages);

    /**
     * @param string $message
     */
    public function addMessage($message);

    /**
     * @return ArrayCollection|string[]
     */
    public function getMessages();

    /**
     * @param string $targetDirectory
     */
    public function setTargetDirectory($targetDirectory);

    /**
     * @return string
     */
    public function getTargetDirectory();

    /**
     * @param bool $overWrite
     */
    public function setOverwrite($overWrite);

    /**
     * @return bool
     */
    public function shouldOverwrite();

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return ArrayCollection|File[]
     */
    public function generate();

    /**
     * Sets up appropriate file contents.
     * @return $this
     */
    public function prepare();

    /**
     * @param string $format
     */
    public function setFormat($format);

    /**
     * @return string
     */
    public function getFormat();

    /**
     * Returns a new instance with all values blank.
     *
     * @return static
     */
    public function reset();
}
