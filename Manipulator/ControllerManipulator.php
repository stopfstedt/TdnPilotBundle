<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\ClassLoader\ClassMapGenerator;
use \SplFileInfo;
use Tdn\PilotBundle\Model\ServiceDefinition;

/**
 * @todo: Separate Test gen from this class and create separate "plugins" to generate the tests.
 * Class ControllerManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class ControllerManipulator extends AbstractServiceManipulator
{
    const NOT_FOUND_TOKEN = 'NOT_FOUND';

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

    /**
     * @var int
     */
    protected $pathDepth;

    public function __construct()
    {
        $this->setResource(false);
        $this->setSwagger(false);
        $this->setGenerateTests(false);
        $this->setForcedTests(false);
        $this->setPathDepth(0);

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
     * @return SplFileInfo
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
     * @param int $depth
     */
    public function setPathDepth($depth)
    {
        $this->pathDepth = $depth;
    }

    /**
     * @return int
     */
    public function getPathDepth()
    {
        return $this->pathDepth;
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
            $this->addDataLoaderServiceFile();//For ilios only.
            $this->addBaseControllerTest();
            $this->addControllerTest();
        }

        $this->addHandlerDependency();

        return $this;
    }

    /**
     * @deprecated
     */
    private function addDataLoaderServiceFile()
    {
        $serviceFile = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'dataloaders.%s',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getFormat()
            )
        );

        $serviceFile
            ->setFilteredContents($this->getServiceFileContents($serviceFile))
            ->setServiceFile(true)
        ;

        $this->addMessage(sprintf(
            'Make sure to load "%s" in your extension file to enable the new services.',
            $serviceFile->getBasename()
        ));

        $this->addFile($serviceFile);
    }

    /**
     * @deprecated
     * @param File $file
     *
     * @return string
     */
    protected function getServiceFileContents(File $file)
    {
        $serviceClass = $this->findClassInFqdnArray(
            $this->getEntity(),
            $this->getDataSeeds()
        );

        $serviceId = sprintf(
            '%s.dataloader.%s',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', ''),
            (string) String::create($this->getEntity())->toLowerCase()
        );

        $paramKey = $serviceId . '.class';

        $definition = new Definition('%' . $paramKey . '%');

        return $this->getServiceFileUtils()
            ->addParameter($paramKey, $serviceClass)
            ->addServiceDefinition(new servicedefinition($serviceId, $definition))
            ->dump($file)
        ;
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

            //Opinionated...
//            if (!function_exists('sqlite_open')) {
//                throw new \RuntimeException(
//                    'PHP Detected no SQLite Support. Please ensure SQLite extension is installed.'
//                );
//            }
        }

        return parent::isValid();
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
                'entity_form_type'       => ($this->isResource() ?
                    (string) String::create($this->getEntity())->lowerCaseFirst() :
                    (string) String::create($this->getEntity() . 'Type')
                    ->underscored()
                    ->toLowerCase())
            ]
        );
    }

    /**
     * @return string
     */
    protected function getAbstractControllerTestContent()
    {
        return $this->getTemplateStrategy()->render(
            'controller/abstract-controller-test.php.twig',
            [
                'entity'                => $this->getEntity(),
                'namespace'             => $this->getBundle()->getNamespace(),
            ]
        );
    }

    protected function getDataLoaderNs()
    {
        return sprintf(
            '%s.dataloader',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', '')
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
                'entity_identifier'      => $this->getEntityIdentifier(),
                'private_fields'         => $this->getPrivateFields(),
                'entity'                 => $this->getEntity(),
                'namespace'              => $this->getBundle()->getNamespace(),
                'resource'               => $this->isResource(),
                'dataloader_service_ns'  => $this->getDataLoaderNs(),
                'fixtures'               => $this->getRelevantFixtures()
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

    /**
     * @return void
     */
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
     * @return array
     */
    private function getFixtures()
    {
        if ($this->getFixturesPath() == null && !$this->shouldForceTests()) {
            throw new \InvalidArgumentException(
                'Fixtures path must be present.'
            );
        }

        return array_keys(ClassMapGenerator::createMap($this->getFixturesPath()->getRealPath(), $this->getPathDepth()));
    }

    /**
     * This will be replaced with support for alice.
     *
     * @deprecated
     * @return array
     */
    private function getDataSeeds()
    {
        if ($this->getDataPath() == null && !$this->shouldForceTests()) {
            throw new \InvalidArgumentException(
                'Data path must be present.'
            );
        }

        return array_keys(ClassMapGenerator::createMap($this->getDataPath()->getRealPath(), $this->getPathDepth()));
    }

    /**
     * @return array
     */
    private function getRelevantFixtures()
    {
        $fixtureNs       = $this->getNamespaceFromFqdn($this->getFixtures()[0]);
        $fixtures        = [];
        $possibleClasses = array_merge(
            [$this->getEntity()],
            array_map(
                function ($v) {
                    return $v['targetEntity'];
                },
                $this->getMetadata()->associationMappings
            )
        );

        foreach ($possibleClasses as $possibleClass) {
            $possibleFixture = $fixtureNs . '\\Load' . $this->getClassFromFqdn($possibleClass) . 'Data';

            if (!in_array($possibleFixture, $this->getFixtures()) && !$this->shouldForceTests()) {
                throw new \RuntimeException(
                    sprintf(
                        'Fixture %s is missing. ' .
                        'If you wish to generate tests with missing fixtures use the --force-tests flag.',
                        $possibleFixture
                    )
                );
            }

            $fixtures[] = $possibleFixture;
        }

        return $fixtures;
    }

    /**
     * @return array|string
     */
    private function getPrivateFields()
    {
        $cleanedProperties = [];
        $classDoc   = $this->getMetadata()->getReflectionClass()->getDocComment();
        /** @var \ReflectionProperty[] $properties */
        $properties = $this->getMetadata()->getReflectionClass()->getProperties();

        $exposeToken = (String::create($classDoc)->contains('ExclusionPolicy("all")', true)) ? 'Expose' : 'Exclude';

        foreach ($properties as $property) {
            $docComment = $property->getDocComment();
            //Ties to JMS Serializer... Must remove with Alice.
            if ($docComment !== null) {
                $docComment = String::create($docComment);
                if ($docComment->contains($exposeToken, true) && $exposeToken == 'Exclude') {
                    $cleanedProperties[] = $property->getName();
                }

                if (!$docComment->contains($exposeToken, true) && $exposeToken == 'Expose') {
                    $cleanedProperties[] = $property->getName();
                }
            }
        }

        return $cleanedProperties;
    }

    /**
     * @param string $fqdn
     * @return string
     */
    private function getClassFromFqdn($fqdn)
    {
        $fqdn = String::create($fqdn);

        if ($fqdn->strrpos('\\') !== false) {
            return (string) $fqdn->substr($fqdn->strrpos('\\') + 1);
        }

        return $fqdn;
    }

    /**
     * @param string $fqdn
     * @return string
     */
    private function getNamespaceFromFqdn($fqdn)
    {
        $fqdn = String::create($fqdn);

        return (string) $fqdn->substr(0, $fqdn->strrpos('\\'));
    }

    /**
     * @param $class
     * @param array $fqdnArray
     *
     * @return string|null
     */
    private function findClassInFqdnArray($class, array $fqdnArray)
    {
        foreach ($fqdnArray as $fqdn) {
            $fqdn = String::create($fqdn);
            if ($fqdn->endsWith($class . 'Data')) {
                return (string) $fqdn;
            }
        }

        if (!$this->shouldForceTests()) {
            throw new \LogicException(
                sprintf(
                    '%s was not found in the array.' . PHP_EOL .
                    '%s',
                    $class,
                    print_r($fqdnArray, true)
                )
            );
        }

        return self::NOT_FOUND_TOKEN;
    }
}
