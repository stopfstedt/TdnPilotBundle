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
     * @return string
     */
    public function dump();
}
