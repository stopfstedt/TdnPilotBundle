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
     * @return static
     */
    public function reset();

    /**
     * @return array
     */
    public static function getSupportedFormats();

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
     * @param ClassMetadata $metadata
     */
    public function setMetadata(ClassMetadata $metadata);

    /**
     * @return ClassMetadata
     */
    public function getMetadata();

    /**
     * @return string
     */
    public function getEntity();

    /**
     * @return string
     */
    public function getEntityNamespace();

    /**
     * @param Collection $files
     */
    public function setFiles(Collection $files);

    /**
     * @param File $file
     */
    public function addFile(File $file);

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
     * @param bool $overwrite
     */
    public function setOverwrite($overwrite);

    /**
     * @return bool
     */
    public function shouldOverwrite();

    /**
     * @param string $format
     */
    public function setFormat($format);

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @throws \RunTimeException
     * @return bool
     */
    public function isValid();

    /**
     * @return $this
     */
    public function prepare();

    /**
     * @return ArrayCollection|File[]
     */
    public function generate();
}
