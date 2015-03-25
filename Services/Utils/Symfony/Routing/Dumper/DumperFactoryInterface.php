<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper;

use Symfony\Component\Routing\RouteCollection;
use Tuck\ConverterBundle\Exception\UnknownFormatException;
use Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\DumperInterface;

/**
 * Interface DumperFactoryInterface
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper
 */
interface DumperFactoryInterface
{
    /**
     * @param string $type
     * @param RouteCollection $routeCollection
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    public function createDumper($type, RouteCollection $routeCollection);
}
