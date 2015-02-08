<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class SonataGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class SonataGenerator extends Generator
{
    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface $bundle The bundle in which to create the class
     * @param string $entity The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param array $options [restSupport => (bool)]
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, array $options = null)
    {
        $dir = $bundle->getPath();
        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Admin/%sAdmin.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$options['overwrite'] && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the sonata admin ' . $target . ' class as it already exists.');
        }

        if(!is_dir("$dir/Admin")) {
            mkdir("$dir/Admin");
        }

        $this->renderFile('sonata/admin.php.twig', $target, array(
            'fields'            => $this->getFieldsFromMetadata($metadata),
            'bundle'            => $bundle->getName(),
            'entity'            => $entity,
            'entity_class'      => $entityClass,
            'namespace'         => $bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace
        ));
    }

    /**
     * @param string $generatedName
     */
    public function setGeneratedName($generatedName)
    {
        return 'Sonata Admin';
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

