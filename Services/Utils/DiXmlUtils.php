<?php

namespace Tdn\PilotBundle\Services\Utils;

/**
 * Class DiXmlUtils
 * @package Tdn\PilotBundle\Manipulator\DependencyInjection
 */
class DiXmlUtils
{
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
