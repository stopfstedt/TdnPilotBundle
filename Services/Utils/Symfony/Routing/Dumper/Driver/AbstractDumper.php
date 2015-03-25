<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class AbstractDumper
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver
 */
abstract class AbstractDumper
{
    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }
}
