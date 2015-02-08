<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\PhpManipulator\TokenStream;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PhpTypes\Type\String;

/**
 * Class HandlerGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class HandlerGenerator extends Generator
{
    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface $bundle The bundle in which to create the class
     * @param string $entity The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param ArrayCollection $options [restSupport => (bool)]
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, ArrayCollection $options = null)
    {
        $dir = $bundle->getPath();

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $this->setGeneratedName($entityClass . "Handler.php");
        $target = sprintf(
            '%s/Handler/%sHandler.php',
            $dir,
            $entityClass
        );
        $this->setFilePath($target);

        if (!is_dir(dirname($target))) {
            mkdir(dirname($target));
        }

        if (!$options->get('overwrite') && file_exists($target)) {
            throw new \RuntimeException(sprintf(
                'Unable to generate the handler class %s as it already exists.',
                $target
            ));
        }

        $this->renderFile(
            'handler/handler.php.twig',
            $target,
            [
                'entity' => $entity,
                'entity_class' => $entityClass,
                'namespace' => $bundle->getNamespace(),
                'entity_namespace' => $entityNamespace,
            ],
            $options->get('overwrite')
        );

        $this->declareService($bundle, $entity);
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

        $handlersFile = sprintf(
            "%s/Resources/config/handlers.xml",
            $dir
        );

        $handlerClass = sprintf(
            "%s\\Handler\\%sHandler",
            $namespace,
            $entityClass
        );

        $paramKey = sprintf(
            "%s.%s.handler.class",
            str_replace("bundle", "", $bundleName),
            $entityName
        );

        $newId = sprintf(
            "%s.%s.handler",
            str_replace("bundle", "", $bundleName),
            $entityName
        );

        $fileName = sprintf(
            "%s/DependencyInjection/%s.php",
            $dir,
            str_replace("Bundle", "Extension", $bundle->getName())
        );

        if (!is_file($handlersFile)) {
            $this->renderFile("config/services.xml.twig", $handlersFile, []);
        }

        $newXML = simplexml_load_file($handlersFile);

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
            $newParamTag[0] = $handlerClass;
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

            $formFactoryTag = $newServiceTag->addChild("argument");
            $formFactoryTag->addAttribute("type", "service");
            $formFactoryTag->addAttribute("id", "form.factory");
        }

        $newXML->saveXML($handlersFile);
        $this->updateDIFile($fileName);
    }

    /**
     * @todo Move into trait or dependency.
     * @param string $fileName
     */
    private function updateDIFile($fileName)
    {
        $loaderInput = PHP_EOL . "\t\t\$xmlLoader = new Loader\\XmlFileLoader(\$container, new FileLocator(__DIR__ . '/../Resources/config'));";
        $toInput =   PHP_EOL . "\t\t\$xmlLoader->load('handlers.xml');" . PHP_EOL . "\t";

        $text = file_get_contents($fileName);

        if (!String::create($text)->contains($loaderInput)) {
            $toInput = $loaderInput . $toInput;
        }

        if (strpos($text, "handlers.xml") == false) {
            $position = strpos($text, "}", strpos($text, "function load("));

            $newContent = substr_replace($text, $toInput, $position, 0);
            file_put_contents($fileName, $newContent);
        }
    }
}
