<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Interface GeneratorInterface
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
interface GeneratorInterface
{
    /**
     * @param BundleInterface $bundle
     * @param string $entity
     * @param ClassMetadataInfo  $metadata
     * @param array  $options
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, array $options = null);

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem);

    /**
     * @param array $skeletonDirs
     */
    public function setSkeletonDirs(array $skeletonDirs);

    /**
     * @param string $generatedName
     */
    public function setGeneratedName($generatedName);

    /**
     * @return string
     */
    public function getGeneratedName();

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath);

    /**
     * @return string
     */
    public function getFilePath();
}
