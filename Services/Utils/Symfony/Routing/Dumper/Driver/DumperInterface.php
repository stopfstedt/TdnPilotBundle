<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver;

use Symfony\Component\Routing\RouteCollection;

/**
 * Interface DumperInterface
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver
 */
interface DumperInterface
{
    /**
     * @param RouteCollection $routes
     * @return string
     */
    public function dump(RouteCollection $routes);
}
