<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\PhpManipulator\TokenStream;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PhpTypes\Type\String;
use Tdn\SfProjectGeneratorBundle\Manipulator\ObjectManipulator;
use Tdn\SfProjectGeneratorBundle\Model\Source;
use Tdn\SfProjectGeneratorBundle\Model\Method;
use Tdn\SfProjectGeneratorBundle\Model\Param;
/**
 * Class ManagerGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class ManagerGenerator extends Generator
{
    /**
     * @param BundleInterface $bundle
     * @param string $entity
     * @param ClassMetadataInfo $metadata
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function generate(
        BundleInterface $bundle,
        $entity,
        ClassMetadataInfo $metadata,
        array $options = null
    ) {
        $dir = $bundle->getPath();

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);
        $pathToEntity = $this->getEntityPath(
            sprintf(
                "%s/Entity/%s",
                $dir,
                $entityClass
            )
        );

        $this->setGeneratedName($entityClass . "Manager.php");
        $target = sprintf(
            '%s/Entity/Manager/%sManager.php',
            $dir,
            $entityClass
        );
        $this->setFilePath($target);

        $interfaceTarget = sprintf(
            '%s/Entity/Manager/%sManagerInterface.php',
            $dir,
            $entityClass
        );

        if (!$options['overwrite'] && file_exists($target)) {
            throw new \RuntimeException(sprintf(
                'Unable to generate the manager class %s as it already exists.',
                $target
            ));
        }

        if(!is_dir(dirname($target))) {
            mkdir(dirname($target));
        }

        /** @var Method $constructorMethod */
        $constructorMethod = $this->getManipulator($pathToEntity)->findMethods(String::create('public'))->get('__construct');
        $params = ($constructorMethod !== null) ? $constructorMethod->getParams() : new ArrayCollection();
        $this->renderFile('manager/manager.php.twig', $target, array(
            'entity'            => $entity,
            'entity_class'      => $entityClass,
            'namespace'         => $bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'entity_construct_params' => $this->generateParams($params),
            'contruct_params'   => $this->generateConstructParams($params)
        ));

        $this->renderFile('manager/interface.php.twig', $interfaceTarget, array(
            'entity'            => $entity,
            'entity_class'      => $entityClass,
            'namespace'         => $bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'entity_construct_params' => $this->generateParams($params),
            'contruct_params'   => $this->generateConstructParams($params)
        ));

        $this->declareService($bundle, $entity);
    }

    /**
     * @param string $path
     *
     * @return \Tdn\PhpTypes\Type\String
     */
    protected function getEntityPath($path)
    {
        return String::create($path)->ensureRight('.php');
    }

    /**
     * Generates string containing params in following format:
     * Interface $param, array $param = [], $param...etc
     *
     * @param Collection $originalParams
     * @return string
     */
    protected function generateParams(Collection $originalParams)
    {
        $params = '';

        /** @var Param $param */
        foreach ($originalParams as $param) {
            $typeHint = ($param->getTypeHint() !== null) ? ' ' . $param->getTypeHint() : '';
            $default = ($param->getDefault() !== null) ? ' = ' . $param->getDefault() . ', ' : ', ';
            $params .= sprintf("%s$%s%s", $typeHint, $param->getVarName(), $default);
        }

        return (string) String::create($params)->removeRight(', ');
    }

    /**
     * Generates string containing params in following format:
     * $param1, $param2, $param3...etc
     *
     * @param Collection $originalParams
     * @return string
     */
    protected function generateConstructParams(Collection $originalParams)
    {
        $params = '';

        /** @var Param $param */
        foreach ($originalParams as $param) {
            $params .= sprintf('$%s, ', $param->getVarName());
        }

        return (string) String::create($params)->removeRight(', ');
    }

    /**
     * @param string $filePath
     * @return ObjectManipulator
     */
    protected function getManipulator($filePath)
    {
        $source = new Source(new SplFileInfo($filePath, null, null), new TokenStream());
        return new ObjectManipulator($source);
    }

    /**
     * @todo Move into trait or dependency.
     * @param BundleInterface $bundle
     * @param string $entity
     */
    protected function declareService(BundleInterface $bundle, $entity)
    {
        $dir = $bundle->getPath();

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);
        $namespace = $bundle->getNamespace();

        $bundleName = strtolower($bundle->getName());
        $entityName = strtolower($entity);

        $managerFile = sprintf(
            "%s/Resources/config/managers.xml",
            $dir
        );

        $managerClass = sprintf(
            "%s\\Entity\\Manager\\%sManager",
            $namespace,
            $entityClass
        );

        $paramKey = sprintf(
            "%s.%s.manager.class",
            str_replace("bundle", "", $bundleName),
            $entityName
        );

        $newId = sprintf(
            "%s.%s.manager",
            str_replace("bundle", "", $bundleName),
            $entityName
        );

        $fileName = sprintf(
            "%s/DependencyInjection/%s.php",
            $dir,
            str_replace("Bundle", "Extension", $bundle->getName())
        );

        if (!is_file($managerFile)) {
            $this->renderFile("config/services.xml.twig", $managerFile, array());
        }

        $newXML = simplexml_load_file($managerFile);

        if (!($parametersTag = $newXML->parameters)) {
            $parametersTag = $newXML->addChild('parameters');
        }

        if (!($servicesTag = $newXML->services)) {
            $servicesTag = $newXML->addChild("services");
        }

        $paramSearch   = $newXML->xpath("//*[@key='$paramKey']");
        $serviceSearch = $newXML->xpath("//*[@id='$newId']");

        if (!$paramSearch) {
            $newParamTag = $parametersTag->addChild("parameter");
            $newParamTag->addAttribute("key", $paramKey);
            $newParamTag[0] = $managerClass;
        }

        if (!$serviceSearch) {
            $newServiceTag = $servicesTag->addChild("service");
            $newServiceTag->addAttribute("id", $newId);
            $newServiceTag->addAttribute("class", "%" . $paramKey . "%");

            $entityManagerTag = $newServiceTag->addChild("argument");
            $entityManagerTag->addAttribute("type", "service");
            $entityManagerTag->addAttribute("id", "doctrine.orm.entity_manager");

            $newServiceTag->addChild(
                "argument",
                sprintf(
                    "%s\\Entity\\%s%s",
                    $namespace,
                    $entityNamespace,
                    $entityClass
                )
            );
        }

        $newXML->saveXML($managerFile);
        $this->updateDIFile($fileName);
    }

    /**
     * @todo Move into trait or dependency.
     * @param string $fileName
     */
    protected function updateDIFile($fileName)
    {
        $loaderInput = PHP_EOL . "\t\t\$xmlLoader = new Loader\\XmlFileLoader(\$container, new FileLocator(__DIR__ . '/../Resources/config'));";
        $toInput =   PHP_EOL . "\t\t\$xmlLoader->load('managers.xml');" . PHP_EOL . "\t";

        $text = file_get_contents($fileName);

        if (!String::create($text)->contains($loaderInput)) {
            $toInput = $loaderInput . $toInput;
        }

        if (strpos($text, "managers.xml") == false) {
            $position = strpos($text, "}", strpos($text, "function load("));

            $newContent = substr_replace($text, $toInput, $position, 0);
            file_put_contents($fileName, $newContent);
        }
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
