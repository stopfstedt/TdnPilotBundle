<?php

namespace Tdn\PilotBundle\Manipulator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Model\GeneratedFile;

/**
 * Class ControllerManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class ControllerManipulator extends AbstractManipulator
{
    /**
     * @var string
     */
    private $routePrefix = '';

    /**
     * @var bool
     */
    private $resource = false;

    /**
     * @var bool
     */
    private $document = false;

    /**
     * @var bool
     */
    private $generateTests = false;

    /**
     * @param string $routePrefix
     */
    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
    }

    /**
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * @return string
     */
    public function getRouteNamePrefix()
    {
        return ($this->getRoutePrefix()) ? str_replace('/', '_', $this->getRoutePrefix()) : '';
    }

    /**
     * @param bool $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return bool
     */
    public function isResource()
    {
        return $this->resource;
    }

    /**
     * @param bool $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return bool
     */
    public function hasDocument()
    {
        return $this->document;
    }

    /**
     * @param bool $generateTests
     */
    public function setGenerateTests($generateTests)
    {
        $this->generateTests = $generateTests;
    }

    /**
     * @return bool
     */
    public function hasGenerateTests()
    {
        return $this->generateTests;
    }

    /**
     * Sets up a controller based on an entity.
     * Sets up controller test files if flag is set.
     * @return $this
     */
    public function prepare()
    {
        $this->addController();

        if ($this->hasGenerateTests()) {
            $this->addControllerTests();
        }

        $this->addHandlerDependency();

        return $this;
    }

    /**
     * @return void
     */
    protected function addController()
    {
        $generatedController = new GeneratedFile();
        $generatedController
            ->setFilename($this->getEntity() . 'Controller')
            ->setExtension('php')
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Controller',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            )) //<TargetDir>|<BundlePath>/Controller
            ->setContents($this->generateControllerFileContent())
        ;

        $this->addGeneratedFile($generatedController);
    }

    /**
     * @return void
     */
    protected function addControllerTests()
    {
        $controllerTest = new GeneratedFile();
        $controllerTest
            ->setFilename($this->getEntity() . 'ControllerTest')
            ->setExtension('php')
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . '%s' . DIRECTORY_SEPARATOR . 'Controller',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                'Tests'
            )) //<TargetDir>|<BundlePath>/Tests/Controller
            ->setContents($this->generateControllerTestContent())
            ->setForceNew(true)
        ;

        $this->addGeneratedFile($controllerTest);
    }

    /**
     * @return string The controller contents
     */
    protected function generateControllerFileContent()
    {
        $idType = $this->getIdentifierType($this->getMetadata());

        return $this->getOutputEngine()->render(
            'controller/controller.php.twig',
            [
                'entity_identifier_type' => $idType,
                'entity_identifier'      => $this->getEntityIdentifier($this->getMetadata()),
                'requirement_regex'      => $this->getRequirementRegex($idType),
                'route_prefix'           => $this->getRoutePrefix(),
                'route_name_prefix'      => $this->getRouteNamePrefix(),
                'bundle'                 => $this->getBundle()->getName(),
                'entity'                 => $this->getEntity(),
                'entity_namespace'       => $this->getEntityNamespace(),
                'identifier'             => $this->getEntityIdentifier($this->getMetadata()),
                'namespace'              => $this->getBundle()->getNamespace(),
                'resource'               => $this->isResource(),
                'document'               => $this->hasDocument(),
                'format'                 => 'yml',
                'form_type'              => $this->getBundle()->getNamespace() .
                    '\\Form\\' . $this->getEntity() . 'Type.php'
            ]
        );
    }

    /**
     * Generates the functional test class only.
     *
     * @return string The file contents
     */
    protected function generateControllerTestContent()
    {
        return $this->getOutputEngine()->render(
            'controller/controller-test.php.twig',
            [
                'namespace' => $this->getBundle()->getNamespace(),
                'entity' => $this->getEntity(),
            ]
        );
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function getIdentifierType(ClassMetadataInfo $metadata)
    {
        if (count($metadata->identifier) !== 1) {
            throw new \InvalidArgumentException(
                'TdnPilotBundle is incompatible with entities that contain more than one identifier or no identifier.'
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
    protected function getEntityIdentifier(ClassMetadataInfo $metadata)
    {
        if (count($metadata->identifier) !== 1) {
            throw new \InvalidArgumentException(
                'TdnPilotBundle is incompatible with entities that contain more than one identifier or no identifier.'
            );
        }

        return array_values($metadata->identifier)[0];
    }

    /**
     * @param string $idType
     * @return string
     */
    protected function getRequirementRegex($idType)
    {
        switch ($idType) {
            case 'string':
                return '\w+';
            case 'int':
            case 'integer':
                return '\d+';
            default:
                return '';
        }
    }

    protected function addHandlerDependency()
    {
        $handlerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . '%sHandler.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new SplFileInfo($handlerFile, null, null));
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ($this->hasGenerateTests()) {
            if (!class_exists('Doctrine\\Bundle\\FixturesBundle\\DoctrineFixturesBundle')) {
                throw new \RuntimeException(
                    'DoctrineFixturesBundle is not installed. Please install it.'
                );
            }

            if (!function_exists('sqlite_open')) {
                throw new \RuntimeException(
                    'PHP Detected no SQLite Support. Please ensure SQLite extension is installed.'
                );
            }
        }

        return parent::isValid();
    }
}
