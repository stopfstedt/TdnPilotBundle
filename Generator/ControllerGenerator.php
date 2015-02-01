<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a REST controller.
 *
 * Class ControllerGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class ControllerGenerator extends Generator
{
    protected $filesystem;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string
     */
    protected $routeNamePrefix;

    /**
     * @var BundleInterface
     */
    protected $bundle;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var ClassMetadataInfo
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @param BundleInterface $bundle
     * @param string $entity
     * @param ClassMetadataInfo $metadata
     * @param ArrayCollection $options
     *
     * @throws \RuntimeException
     */
    public function generate(
        BundleInterface $bundle,
        $entity,
        ClassMetadataInfo $metadata,
        ArrayCollection $options = null
    ) {
        $this->routePrefix = $options->get('route-prefix');
        $this->routeNamePrefix = str_replace('/', '_', $options->get('route-prefix'));
        $this->actions = ['getById', 'getAll', 'post', 'put', 'delete'];

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException(
                'The REST Controller generator does not support entity classes with multiple primary keys.'
            );
        }

        $this->entity = $entity;
        $this->bundle = $bundle;
        $this->metadata = $metadata;
        $this->setFormat('yml');

        $this->generateControllerClass($options->get('overwrite'), $options->get('document'), $options->get('resource'), $metadata);
    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    private function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

    /**
     * Generates the controller class only.
     *
     * @param $forceOverwrite
     * @param $document
     * @param $resource
     * @param ClassMetadataInfo $metadata
     */
    protected function generateControllerClass($forceOverwrite, $document, $resource, ClassMetadataInfo $metadata)
    {
        $dir = $this->bundle->getPath();
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);
        $target = sprintf(
            '%s/Controller/%sController.php',
            $dir,
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller ' . $target .' as it already exists. Use --overwrite to overwrite file.');
        }

        $this->setGeneratedName($entityClass . "Controller.php");
        $this->setFilePath($target);

        $idType = $this->getIdentifierType($metadata);
        $this->renderFile(
            'controller/controller.php.twig',
            $target,
            [
                'entity_identifier_type' => $idType,
                'entity_identifier'      => $this->getEntityIdentifier($metadata),
                'requirement_regex'      => $this->getRequirementRegex($idType),
                'route_prefix' => $this->routePrefix,
                'route_name_prefix' => $this->routeNamePrefix,
                'bundle' => $this->bundle->getName(),
                'entity' => $this->entity,
                'identifier' => array_keys($metadata->identifier)[0],
                'entity_class' => $entityClass,
                'namespace' => $this->bundle->getNamespace(),
                'entity_namespace' => $entityNamespace,
                'format' => $this->format,
                'resource' => $resource,
                'document' => $document,
                'form_type' => $this->bundle->getNamespace() . "\\Form\\" . $this->entity . "Type.php"
            ],
            $forceOverwrite
        );
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @throws \InvalidArgumentException
     * @return string
     */
    private function getIdentifierType(ClassMetadataInfo $metadata)
    {
        if (count($metadata->identifier) !== 1) {
            throw new \InvalidArgumentException(
                'SfProjectGeneratorBundle is incompatible with entities that contain more than one identifier or no identifier.'
            );
        }

        $identifier = array_values($metadata->identifier)[0];
        foreach ($metadata->fieldMappings as $field) {
                if ($field['fieldName'] == $identifier) {
                    return $field['type'];
                }
        }

        return null;
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @return mixed
     */
    private function getEntityIdentifier(ClassMetadataInfo $metadata)
    {
        if (count($metadata->identifier) !== 1) {
            throw new \InvalidArgumentException(
                'SfProjectGeneratorBundle is incompatible with entities that contain more than one identifier or no identifier.'
            );
        }

        return array_values($metadata->identifier)[0];
    }

    /**
     * @param string $idType
     * @return string
     */
    private function getRequirementRegex($idType)
    {
        switch ($idType) {
            case 'string':
                return '\w+';
            case 'interger':
                return '\d+';
            default:
                return '';
        }
    }

    /**
     * Generates the functional test class only.
     */
    public function generateTestClass()
    {
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $dir = $this->bundle->getPath() . '/Tests/Controller';
        $target = $dir . '/' . str_replace('\\', '/', $entityNamespace) . '/' . $entityClass . 'ControllerTest.php';

        $this->renderFile(
            'controller/controller-test.php.twig',
            $target,
            [
                'route_prefix' => $this->routePrefix,
                'route_name_prefix' => $this->routeNamePrefix,
                'entity' => $this->entity,
                'bundle' => $this->bundle->getName(),
                'entity_class' => $entityClass,
                'namespace' => $this->bundle->getNamespace(),
                'entity_namespace' => $entityNamespace,
                'actions' => $this->actions,
                'form_type_name' => strtolower(
                    str_replace('\\', '_', $this->bundle->getNamespace()) . ($parts ? '_' : '') . implode(
                        '_',
                        $parts
                    ) . '_' . $entityClass . 'Type'
                ),
            ]
        );
    }
}
