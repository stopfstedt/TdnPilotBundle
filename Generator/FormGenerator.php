<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class FormGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class FormGenerator extends Generator
{
    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param ArrayCollection   $options  [rest-support => (bool)]
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, ArrayCollection $options = null)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->setGeneratedName($entityClass.'Type');
        $dirPath         = $bundle->getPath().'/Form';
        $this->setFilePath($dirPath.'/'.str_replace('\\', '/', $entity).'Type.php');

        if (file_exists($this->filePath) && !$options->get('overwrite')) {
            throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the file: %s', $this->generatedName, $this->filePath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile(
            'form/FormType.php.twig',
            $this->filePath,
            [
                'fields'           => $this->getFieldsFromMetadata($metadata),
                'associations'     => $metadata->associationMappings,
                'namespace'        => $bundle->getNamespace(),
                'entity_namespace' => implode('\\', $parts),
                'entity_class'     => $entityClass,
                'bundle'           => $bundle->getName(),
                'rest_support'     => $options->get('rest-support'),
                'form_class'       => $this->generatedName,
                'form_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.substr($this->generatedName, 0, -4)),
            ],
            $options->get('overwrite')
        );

        $target = sprintf('%s/Exception/InvalidFormException.php', $bundle->getPath());
        if (!is_file($target)) {
            $this->generateExceptionClass($bundle, $target);
        }

    }

    protected function generateExceptionClass(BundleInterface $bundle, $target)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target));
        }

        $this->renderFile(
            'form/form_exception.php.twig',
            $target,
            [
                'namespace' => $bundle->getNamespace()
            ]
        );
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $generatedName
     */
    public function setGeneratedName($generatedName)
    {
        $this->generatedName = $generatedName;
    }
}
