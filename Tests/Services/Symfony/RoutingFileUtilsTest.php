<?php

namespace Tdn\PilotBundle\Tests\Services\Symfony;

use Symfony\Component\Routing\RouteCollection;
use Tdn\PilotBundle\Services\Utils\Symfony\RoutingFileUtils;

/**
 * Class RoutingFileUtilsTest
 * @package Tdn\PilotBundle\Tests\Services\Symfony
 */
class RoutingFileUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoutingFileUtils
     */
    protected $routingFileUtils;

    protected function setUp()
    {
        $this->routingFileUtils = new RoutingFileUtils();
    }

    public function testAddCollection()
    {
        $collection = new RouteCollection();
        $this->routingFileUtils->addCollection($collection);

    }

    public function testRoutesAsXml()
    {

    }

    public function testRoutesAsYaml()
    {

    }
}
