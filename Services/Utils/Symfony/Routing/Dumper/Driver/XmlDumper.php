<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver;

/**
 * Class XmlDumper
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver
 */
class XmlDumper extends AbstractDumper implements DumperInterface
{
    /**
     * Dumps the xml string representation of a Route Collection.
     *
     * @return string
     */
    public function dump()
    {
        $routesXml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8" ?>' .
            '<routes xmlns="http://symfony.com/schema/routing"' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' .
            'xsi:schemaLocation="http://symfony.com/schema/routing ' .
            'http://symfony.com/schema/routing/routing-1.0.xsd" />'
        );

        foreach ($this->routeCollection->all() as $name => $route) {
            $routeXml = $routesXml->addChild('route');
            $routeXml->addAttribute('id', $name);
            $routeXml->addAttribute('path', $route->getPath());

            foreach ($route->getDefaults() as $key => $value) {
                $defaultXml = $routeXml->addChild('default', $value);
                $defaultXml->addAttribute('key', $key);
            }

            if (count($route->getRequirements())) {
                foreach ($route->getRequirements() as $key => $value) {
                    $requirementXml = $routeXml->addChild('requirement', $value);
                    $requirementXml->addAttribute('key', $key);
                }
            }

            if (count($route->getOptions())) {
                foreach ($route->getOptions() as $key => $value) {
                    $optionXml = $routeXml->addChild('option', $value);
                    $optionXml->addAttribute('key', $key);
                }
            }

            if ($route->getHost()) {
                $routeXml->addAttribute('host', $route->getHost());
            }

            if (count($route->getSchemes())) {
                $routeXml->addAttribute('schemes', implode(', ', $route->getSchemes()));
            }

            if (count($route->getMethods())) {
                $routeXml->addAttribute('methods', implode(', ', $route->getMethods()));
            }

            if ($route->getCondition()) {
                $routeXml->addChild('condition', $route->getCondition());
            }
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($routesXml->asXML());
        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
