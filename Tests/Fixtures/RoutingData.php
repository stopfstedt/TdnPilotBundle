<?php

namespace Tdn\PilotBundle\Tests\Fixtures;

/**
 * Static data fixture for routing tests.
 *
 * Class RoutingData
 * @package Tdn\PilotBundle\Tests\Fixtures
 */
class RoutingData
{
    const ROUTING_FILE = <<<'ROUTING_FILE'
api_foo_v1:
    resource: "@FooBarBundle/Controller/FooController.php"
    type:     rest
    prefix:   /v1
    defaults: {_format:json}

ROUTING_FILE;

}
