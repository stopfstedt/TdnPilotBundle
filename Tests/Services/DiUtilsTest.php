<?php

namespace Tdn\PilotBundle\Tests\Services;

use Tdn\PilotBundle\Services\Utils\DiUtils;
use Tdn\PilotBundle\Tests\Fixtures\DiUtilsData;

/**
 * Class DiUtilsTest
 * @package Tdn\PilotBundle\Tests\Services
 */
class DiUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DiUtils
     */
    protected $diUtils;

    protected function setUp()
    {
        $this->diUtils = new DiUtils();
    }

    public function testParameters()
    {
        list ($key, $value) = $this->getParameter();
        $this->diUtils->addParameter($key, $value);
        $this->diUtils->getParameters()->contains($key);
        $this->assertEquals($value, $this->diUtils->getParameters()->get($key));
        $this->diUtils->getParameters()->remove($key);
        $this->assertEquals(0, $this->diUtils->getParameters()->count());
    }

    public function testServices()
    {
        list($id, $service) = $this->getService('KEY');
        $this->diUtils->addService($id, $service);
        $this->assertEquals($service, $this->diUtils->getServices()->get($id));
        $this->diUtils->getServices()->remove($id);
        $this->assertEquals(0, $this->diUtils->getServices()->count());
    }

    public function testYaml()
    {
        list ($key, $value) = $this->getParameter();
        list($id, $service) = $this->getService($key);
        $this->diUtils->addParameter($key, $value);
        $this->diUtils->addService($id, $service);

        $this->assertEquals(DiUtilsData::YAML, $this->diUtils->getYaml());
    }

    public function testXml()
    {
        list ($key, $value) = $this->getParameter();
        list($id, $service) = $this->getService($key);
        $this->diUtils->addParameter($key, $value);
        $this->diUtils->addService($id, $service);

        $this->assertEquals(DiUtilsData::XML, $this->diUtils->getXml());
    }

    protected function saveTest($file)
    {
        list ($key, $value) = $this->getParameter();
        list($id, $service) = $this->getService($key);
        $this->diUtils->addParameter($key, $value);
        $this->diUtils->addService($id, $service);
        $this->diUtils->save($file);

        $this->assertEquals(DiUtilsData::YAML, file_get_contents($file));
    }

    protected function loadTest($file)
    {
        //Copy pre-made file over to tmp dir
        $this->diUtils->load($file);
        $this->assertEquals($this->getParameters(), $this->diUtils->getParameters());
        $this->assertEquals($this->getServices(), $this->diUtils->getServices());
    }

    /**
     * @param string $key
     *
     * @return array
     */
    protected function getService($key = null)
    {
        $id = 'foo_bar.manager.foo_manager';
        $service['arguments'] = [
            '@doctrine',
            'Foo\BarBundle\Entity\Foo'
        ];

        if ($key) {
            $service['class'] = '%' . $key . '%';
        }

        krsort($service, SORT_STRING);

        return [$id, $service];
    }

    /**
     * @return array
     */
    protected function getParameter()
    {
        $key = 'foo_bar.manager.foo_manager.class';
        $value = 'Foo\BarBundle\Entity\Manager\FooManager';

        return [$key, $value];
    }
}
