<?php

namespace Tdn\PilotBundle\Tests\Services\Symfony;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class RoutingFileUtilsTest
 * @package Tdn\PilotBundle\Tests\Services\Symfony
 */
class RoutingFileUtilsTest extends \PHPUnit_Framework_TestCase
{
    protected $routingFileUtils;

    protected function setUp()
    {
        $this->routingFileUtils = new RoutingFileUtils();
    }

    public function testAddCollection()
    {
        $collection = new RouteCollection();
        $this->routingFileUtils->addRouteCollection($collection);
        $this->assertContains($collection, $this->routingFileUtils->getRouteCollections());
    }

    public function testRoutesAsXml()
    {

    }

    public function testRoutesAsYaml()
    {

    }
}
