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

    }

    /**
     * Generates the admin class only.
     *
     */
    public function generateAdminClass(ClassMetadataInfo $metadata, $forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Admin/%s/%sAdmin.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the sonata admin ' . $target . ' class as it already exists.');
        }

        if(!is_dir("$dir/Admin")) {
            mkdir("$dir/Admin");
        }

        $this->renderFile('sonata/admin.php.twig', $target, array(
            'fields'            => $this->getFieldsFromMetadata($metadata),
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace
        ));
    }

}

