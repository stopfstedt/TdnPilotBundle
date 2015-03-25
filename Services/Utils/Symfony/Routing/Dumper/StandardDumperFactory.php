<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper;

use Symfony\Component\Routing\RouteCollection;
use Tuck\ConverterBundle\Exception\UnknownFormatException;
use Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\DumperInterface;

/**
 * Class StandardDumperFactory
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper
 */
class StandardDumperFactory implements DumperFactoryInterface
{
    protected $dumperMap = [
        'xml'  => '\Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\XmlDumper',
        'yaml' => '\Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\YamlDumper',
        'yml'  => '\Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\YamlDumper'
    ];

    /**
     * @param string $type
     * @param RouteCollection $routeCollection
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    public function createDumper($type, RouteCollection $routeCollection)
    {
        $class = $this->getClassFromType($type);

        return new $class($routeCollection);
    }

    /**
     * @param string $type
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    protected function getClassFromType($type)
    {
        if (!isset($this->dumperMap[$type])) {
            throw UnknownFormatException::create($type);
        }

        return $this->dumperMap[$type];
    }
}
