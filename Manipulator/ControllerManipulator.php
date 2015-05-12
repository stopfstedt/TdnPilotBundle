<?php

namespace Tdn\PilotBundle\Manipulator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;
use \SplFileInfo;

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

    /**
     * @var SplFileInfo
     */
    protected $fixturesPath;

    /**
     * @var bool
     */
    protected $forcedTests;

    /**
     * @var SplFileInfo
     */
    protected $dataPath;

    public function __construct()
    {
        $this->setResource(false);
        $this->setSwagger(false);
        $this->setGenerateTests(false);
        $this->setForcedTests(false);

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
     * @param SplFileInfo $fixturesPath
     */
    public function setFixturesPath(SplFileInfo $fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

    /**
     * @return string
     */
    public function getFixturesPath()
    {
        return $this->fixturesPath;
    }

    /**
     * @param bool $forcedTests
     */
    public function setForcedTests($forcedTests)
    {
        $this->forcedTests = $forcedTests;
    }

    /**
     * @return bool
     */
    public function shouldForceTests()
    {
        return $this->forcedTests;
    }

    /**
     * @param SplFileInfo $dataPath
     */
    public function setDataPath(SplFileInfo $dataPath)
    {
        $this->dataPath = $dataPath;
    }

    /**
     * @return SplFileInfo
     */
    public function getDataPath()
    {
        return $this->dataPath;
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
            $this->addBaseControllerTest();
            $this->addControllerTest();
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
                ($this->getTargetDirectory()) ?: realpath($this->getBundle()->getPath()),
                $this->getEntity()
            )
        );

        $generatedController->setFilteredContents($this->getControllerContent());

        $this->addFile($generatedController);
    }

    /**
     * @return void
     */
    protected function addControllerTest()
    {
        $controllerTest = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Controller' .
                DIRECTORY_SEPARATOR . '%sControllerTest.php',
                ($this->getTargetDirectory()) ?: realpath($this->getBundle()->getPath()),
                $this->getEntity()
            )
        );

        $controllerTest
            ->setFilteredContents($this->getControllerTestContent())
            ->setAuxFile(true)
        ;

        $this->addFile($controllerTest);
    }

    /**
     * @return void
     */
    protected function addBaseControllerTest()
    {
        $abstractControllerTest = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Controller' .
                DIRECTORY_SEPARATOR . 'AbstractControllerTest.php',
                ($this->getTargetDirectory()) ?: realpath($this->getBundle()->getPath())
            )
        );

        //File created only once.
        if (!is_file($abstractControllerTest->getRealPath()) || $this->shouldOverwrite()) {
            $abstractControllerTest->setFilteredContents($this->getAbstractControllerTestContent());
            $this->addFile($abstractControllerTest);
        }
    }

    /**
     * @return string The controller contents
     */
    protected function getControllerContent()
    {
        $idType = $this->getIdentifierType($this->getMetadata());

        return $this->getTemplateStrategy()->render(
            'controller/controller.php.twig',
            [
                'entity_identifier_type' => $idType,
                'entity_identifier'      => $this->getEntityIdentifier(),
                'requirement_regex'      => $this->getRequirementRegex($idType),
                'route_prefix'           => $this->getRoutePrefix(),
                'route_name_prefix'      => $this->getRouteNamePrefix(),
                'bundle'                 => $this->getBundle()->getName(),
                'entity'                 => $this->getEntity(),
                'entity_namespace'       => $this->getEntityNamespace(),
                'namespace'              => $this->getBundle()->getNamespace(),
                'resource'               => $this->isResource(),
                'swagger'                => $this->hasSwagger(),
                'format'                 => $this->getFormat(),
                'form_type'              => $this->getBundle()->getNamespace() .
                    '\\Form\\Type\\' . $this->getEntity() . 'Type',
                'entity_form_type'       => (string) String::create($this->getEntity() . 'Type')
                    ->underscored()
                    ->toLowerCase()
            ]
        );
    }

    protected function getAbstractControllerTestContent()
    {
        return $this->getTemplateStrategy()->render(
            'controller/abstract-controller-test.php.twig',
            [
                'namespace' => $this->getBundle()->getNamespace()
            ]
        );
    }

    /**
     * Generates the functional test class only.
     *
     * @return string The file contents
     */
    protected function getControllerTestContent()
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
            ($this->getTargetDirectory()) ?: realpath($this->getBundle()->getPath()),
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
