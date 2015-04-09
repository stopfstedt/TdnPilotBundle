<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver;

use Symfony\Component\Routing\RouteCollection;

class XmlDumper implements DumperInterface
{
    public function dump(RouteCollection $routes)
    {
        $routesXml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8" ?>' .
            '<routes mlns="http://symfony.co/schema/routing"' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' .
            'xsi:schemaLocation="http://symfony.com/schema/routing ' .
            'http://symfony.com/schema/routing/routing-1.0.xsd" />'
        );

        foreach ($routes->all() as $name => $route) {

        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($routesXml->asXML());
        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
