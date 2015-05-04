<?php

namespace Tdn\PilotBundle\Tests\Services\Symfony\Routing\Dumper\Driver;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\YamlDumper;

/**
 * Class YamlDumperTest
 * @package Tdn\PilotBundle\Tests\Services\Symfony\Routing\Dumper\Driver
 */
class YamlDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fixtureDirPath;

    /**
     * @var RouteCollection
     */
    private $routeCollection;

    protected function setUp()
    {
        parent::setUp();

        $this->routeCollection = $this->getRouteCollection();
        $this->fixtureDirPath = realpath(__DIR__ . '/../../../../../Fixtures/static');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->routeCollection = null;
        $this->fixtureDirPath = null;
    }

    public function testExport()
    {
        $dumper = new YamlDumper($this->routeCollection);
        $this->assertEquals($dumper->dump(), file_get_contents($this->fixtureDirPath . '/routing.yml'));
    }

    private function getRouteCollection()
    {
        $collection = new RouteCollection();

        // defaults and requirements
        $collection->add('foo', new Route(
            '/foo/{bar}',
            array('def' => 'test'),
            array('bar' => 'baz|symfony')
        ));
        // defaults parameters in pattern
        $collection->add('foobar', new Route(
            '/foo/{bar}',
            array('bar' => 'toto')
        ));
        // method requirement
        $collection->add('bar', new Route(
            '/bar/{foo}',
            array(),
            array('_method' => 'GET|head')
        ));
        // method requirement (again)
        $collection->add('baragain', new Route(
            '/baragain/{foo}',
            array(),
            array('_method' => 'get|post')
        ));
        // simple
        $collection->add('baz', new Route(
            '/test/baz'
        ));
        // simple with extension
        $collection->add('baz2', new Route(
            '/test/baz.html'
        ));
        // trailing slash
        $collection->add('baz3', new Route(
            '/test/baz3/'
        ));
        // trailing slash with variable
        $collection->add('baz4', new Route(
            '/test/{foo}/'
        ));
        // trailing slash and safe method
        $collection->add('baz5', new Route(
            '/test/{foo}/',
            array(),
            array('_method' => 'get')
        ));
        // trailing slash and unsafe method
        $collection->add('baz5unsafe', new Route(
            '/testunsafe/{foo}/',
            array(),
            array('_method' => 'post')
        ));
        // complex
        $collection->add('baz6', new Route(
            '/test/baz',
            array('foo' => 'bar baz')
        ));
        // space in path
        $collection->add('baz7', new Route(
            '/te st/baz'
        ));
        // space preceded with \ in path
        $collection->add('baz8', new Route(
            '/te\\ st/baz'
        ));
        // space preceded with \ in requirement
        $collection->add('baz9', new Route(
            '/test/{baz}',
            array(),
            array(
                'baz' => 'te\\\\ st',
            )
        ));

        $collection1 = new RouteCollection();

        $route1 = new Route('/route1', array(), array(), array(), 'a.example.com');
        $collection1->add('route1', $route1);

        $collection2 = new RouteCollection();

        $route2 = new Route('/route2', array(), array(), array(), 'a.example.com');
        $collection2->add('route2', $route2);

        $route3 = new Route('/route3', array(), array(), array(), 'b.example.com');
        $collection2->add('route3', $route3);

        $collection2->addPrefix('/c2');
        $collection1->addCollection($collection2);

        $route4 = new Route('/route4', array(), array(), array(), 'a.example.com');
        $collection1->add('route4', $route4);

        $route5 = new Route('/route5', array(), array(), array(), 'c.example.com');
        $collection1->add('route5', $route5);

        $route6 = new Route('/route6', array(), array(), array(), null);
        $collection1->add('route6', $route6);

        $collection->addCollection($collection1);

        // host and variables

        $collection1 = new RouteCollection();

        $route11 = new Route('/route11', array(), array(), array(), '{var1}.example.com');
        $collection1->add('route11', $route11);

        $route12 = new Route('/route12', array('var1' => 'val'), array(), array(), '{var1}.example.com');
        $collection1->add('route12', $route12);

        $route13 = new Route('/route13/{name}', array(), array(), array(), '{var1}.example.com');
        $collection1->add('route13', $route13);

        $route14 = new Route('/route14/{name}', array('var1' => 'val'), array(), array(), '{var1}.example.com');
        $collection1->add('route14', $route14);

        $route15 = new Route('/route15/{name}', array(), array(), array(), 'c.example.com');
        $collection1->add('route15', $route15);

        $route16 = new Route('/route16/{name}', array('var1' => 'val'), array(), array(), null);
        $collection1->add('route16', $route16);

        $route17 = new Route('/route17', array(), array(), array(), null);
        $collection1->add('route17', $route17);

        $collection->addCollection($collection1);

        return $collection;
    }
}
