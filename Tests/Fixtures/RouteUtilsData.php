<?php

namespace Tdn\PilotBundle\Tests\Fixtures;

/**
 * Static data fixture for routes.yml/routes.xml tests.
 *
 * Class RouteUtilsData
 * @package Tdn\PilotBundle\Tests\Fixtures
 */
class RouteUtilsData
{
    const YAML = <<<'YAML'
api_test:
    resource: "@ExcaliburAppBundle/Controller/TestController.php"
    type:     rest
    prefix:   /
    defaults: {_format:json}
api_user:
    resource: "@ExcaliburAppBundle/Controller/UserController.php"
    type:     rest
    prefix:   /
    defaults: {_format:json}

YAML;

    const XML = <<<'XML'

XML;

}
