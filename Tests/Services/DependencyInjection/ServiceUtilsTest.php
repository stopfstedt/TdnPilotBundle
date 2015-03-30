<?php

namespace Tdn\PilotBundle\Tests\Services\DependencyInjection;

use Tuck\ConverterBundle\ConfigFormatConverter;
use Tuck\ConverterBundle\Dumper\StandardDumperFactory;
use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\Loader\StandardLoaderFactory;
use Tdn\PilotBundle\Services\DependencyInjection\ServiceUtils;
use Tdn\PilotBundle\Tests\Fixtures\ServiceUtilsData;

/**
 * Class ServiceUtilsTest
 * @package Tdn\PilotBundle\Tests\Services\DependencyInjection
 */
class ServiceUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceUtils
     */
    protected $diUtils;

    protected function setUp()
    {
        $this->diUtils = new ServiceUtils($this->getFormatConverter());
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

        $this->assertEquals(ServiceUtilsData::YAML, $this->diUtils->getYaml());
    }

    public function testXml()
    {
        list ($key, $value) = $this->getParameter();
        list($id, $service) = $this->getService($key);
        $this->diUtils->addParameter($key, $value);
        $this->diUtils->addService($id, $service);

        $this->assertEquals(ServiceUtilsData::XML, $this->diUtils->getXml());
    }

    protected function saveTest($file)
    {
        list ($key, $value) = $this->getParameter();
        list($id, $service) = $this->getService($key);
        $this->diUtils->addParameter($key, $value);
        $this->diUtils->addService($id, $service);
        $this->diUtils->save($file);

        $this->assertEquals(ServiceUtilsData::YAML, file_get_contents($file));
    }

//    protected function loadTest($file)
//    {
//        //Copy pre-made file over to tmp dir
//        $this->serviceUtils->load($file);
//        $this->assertEquals($this->getParameters(), $this->serviceUtils->getParameters());
//        $this->assertEquals($this->getServices(), $this->serviceUtils->getServices());
//    }

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

    /**
     * @return ConfigFormatConverter
     */
    protected function getFormatConverter()
    {
        return new ConfigFormatConverter(
            new StandardLoaderFactory(),
            new StandardDumperFactory(),
            new SysTempFileFactory()
        );
    }
}
