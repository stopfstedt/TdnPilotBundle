<?php

namespace Tdn\PilotBundle\Services\Utils\DependencyInjection;

use Symfony\Component\Finder\SplFileInfo;
use Tdn\PhpTypes\Type\String;

/**
 * Class DiXmlManipulator
 * @package Tdn\PilotBundle\Manipulator\DependencyInjection
 */
class DiXmlManipulator
{
    /**
     * @param string $extensionFile
     * @param string $toLoad
     */
    public function updateDiFile($extensionFile, $toLoad)
    {
        $loader = '$xmlLoader';
        //Add switch that returns string for appropriate loader.
        $loaderInput = sprintf(
            PHP_EOL .
            "\t\t\%s = new Loader\\XmlFileLoader(\$container, new FileLocator(__DIR__ . '/../Resources/config'));",
            $loader
        );

        $toInput = sprintf(
            PHP_EOL .
            "\t\t%s->load('%s');" .
            PHP_EOL,
            $loader,
            $toLoad
        );

        $file = new SplFileInfo($extensionFile, null, null);
        $text = $file->getContents();

        //This is weak. Must fix later.
        if (!String::create($text)->subStrFromTo('function load(', '}' . PHP_EOL)->contains($loader)) {
            $toInput = $loaderInput . $toInput;
        }
        //This is weak. Must fix later.
        if (strpos($text, $toLoad) === false) {
            $position = strpos($text, '}', strpos($text, 'function load('));

            $newContent = substr_replace($text, $toInput, $position, 0);

            file_put_contents($extensionFile, $newContent);
        }
    }

    /**
     * @param \SimpleXMLElement $newXML
     * @param string $serviceClass
     * @param string $paramKey
     * @param string $serviceId
     */
    public function setDiXmlTags(\SimpleXMLElement &$newXML, $serviceClass, $paramKey, $serviceId)
    {
        $parameters = ($newXML->parameters !== null) ? $newXML->parameters : $newXML->addChild('parameters');
        $services   = ($newXML->services !== null) ? $newXML->services : $newXML->addChild('services');

        try {
            $this->getDiXmlParameterTag($paramKey, $newXML);
        } catch (\InvalidArgumentException $e) {
            $parameter = $parameters->addChild('parameter', $serviceClass);
            $parameter->addAttribute('key', $paramKey);
        }

        try {
            $this->getDiXmlServiceTag($serviceId, $newXML);
        } catch (\InvalidArgumentException $e) {
            $service = $services->addChild('service');
            $service->addAttribute('id', $serviceId);
            $service->addAttribute('class', '%' . $paramKey . '%');
        }
    }

    /**
     * @param string $needle
     * @param \SimpleXMLElement $hayStack
     *
     * @return \SimpleXMLElement
     */
    public function getDiXmlParameterTag($needle, \SimpleXMLElement $hayStack)
    {
        foreach ($hayStack->xpath("//*[@key='$needle']") as $node) {
            return $node;
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Could not find %s in %s.',
                $needle,
                $hayStack->asXML()
            )
        );
    }

    /**
     * @param string $needle
     * @param \SimpleXMLElement $hayStack
     *
     * @return \SimpleXMLElement
     */
    public function getDiXmlServiceTag($needle, \SimpleXMLElement $hayStack)
    {
        foreach ($hayStack->xpath("//*[@id='$needle']") as $node) {
            return $node;
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Could not find %s in %s.',
                $needle,
                $hayStack->asXML()
            )
        );
    }

    /**
     * @param \SimpleXMLElement $service
     * @return void
     */
    public function addEmArgTo(\SimpleXMLElement &$service)
    {
        $emArgTag   = $service->addChild('argument');
        $emArgTag->addAttribute('type', 'service');
        $emArgTag->addAttribute('id', 'doctrine.orm.entity_manager');
    }

    /**
     * @param \SimpleXMLElement $service
     * @param string            $bundleNs
     * @param string            $entityNs
     * @param string            $entity
     *
     * @return void
     */
    public function addClassArgTo(\SimpleXMLElement &$service, $bundleNs, $entityNs, $entity)
    {
        $service->addChild(
            'argument',
            sprintf(
                '%s\\Entity\\%s%s',
                $bundleNs,
                $entityNs,
                $entity
            )
        );
    }

    /**
     * @param string $output
     *
     * @return string
     */
    public function formatOutput($output)
    {
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($output);

        return $dom->saveXML();
    }
}
