<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class EntityGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class EntityGenerator extends Generator
{
    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param array             $options
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, array $options = null)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);
        $this->generatedName = $entityClass;
        $dirPath         = $bundle->getPath().'/Entity';
        $this->filePath = $dirPath.'/'.str_replace('\\', '/', $entity).'Interface.php';

        $this->renderFile('entity/interface.php.twig', $this->filePath, array(
            'namespace'        => $bundle->getNamespace(),
            //implement
        ));
    }

    /**
     * @param string $generatedName
     */
    public function setGeneratedName($generatedName)
    {
        // TODO: Implement setGeneratedName() method.
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        // TODO: Implement setFilePath() method.
    }
}
