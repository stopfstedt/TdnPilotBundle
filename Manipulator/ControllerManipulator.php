<?php

namespace Tdn\PilotBundle\Manipulator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;

/**
 * Class ControllerManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class ControllerManipulator extends AbstractManipulator
{
    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var bool
     */
    private $resource;

    /**
     * @var bool
     */
    private $swagger;

    /**
     * @var bool
     */
    private $generateTests;

    public function __construct()
    {
        $this->setResource(false);
        $this->setSwagger(false);
        $this->setGenerateTests(false);

        parent::__construct();
    }

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
     * @param bool $swagger
     */
    public function setSwagger($swagger)
    {
        $this->swagger = $swagger;
    }

    /**
     * @return bool
     */
    public function hasSwagger()
    {
        return $this->swagger;
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
    public function shouldGenerateTests()
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

        if ($this->shouldGenerateTests()) {
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
        $generatedController = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '%sController.php',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getEntity()
            )
        );

        $generatedController->setContents($this->generateControllerFileContent());

        $this->addFile($generatedController);
    }

    /**
     * @return void
     */
    protected function addControllerTests()
    {
        $controllerTest = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . '%s' . DIRECTORY_SEPARATOR . 'Controller' .
                DIRECTORY_SEPARATOR . '%sControllerTest.php',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                'Tests',
                $this->getEntity()
            )
        );

        $controllerTest
            ->setContents($this->generateControllerTestContent())
            ->setAuxFile(true)
        ;

        $this->addFile($controllerTest);
    }

    /**
     * @return string The controller contents
     */
    protected function generateControllerFileContent()
    {
        $idType = $this->getIdentifierType($this->getMetadata());

        return $this->getTemplateStrategy()->render(
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
                'swagger'                => $this->hasSwagger(),
                'format'                 => 'yml',
                'form_type'              => $this->getBundle()->getNamespace() .
                    '\\Form\\Type\\' . $this->getEntity() . 'Type',
                'entity_form_type'       => (string) String::create($this->getEntity() . 'Type')
                    ->underscored()
                    ->toLowerCase()
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
        return $this->getTemplateStrategy()->render(
            'controller/controller-test.php.twig',
            [
                'namespace' => $this->getBundle()->getNamespace(),
                'entity' => $this->getEntity(),
            ]
        );
    }

    /**
     * @param ClassMetadata $metadata
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function getIdentifierType(ClassMetadata $metadata)
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
     * @param ClassMetadata $metadata
     * @return mixed
     */
    protected function getEntityIdentifier(ClassMetadata $metadata)
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

        $this->addFileDependency(new File($handlerFile));
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ($this->shouldGenerateTests()) {
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
