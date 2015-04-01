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
 * @package Tdn\PilotBundle\Tests\Services\FormatConverter
 */
class ServiceUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceUtils
     */
    protected $serviceUtils;

    protected function setUp()
    {
        $this->serviceUtils = new ServiceUtils($this->getFormatConverter());
    }

    public function testParameters()
    {
        $this->assertEmpty($this->serviceUtils->getParameters()->toArray());

        list ($key, $value) = $this->getParameter();
        $this->serviceUtils->addParameter($key, $value);
        $this->serviceUtils->getParameters()->contains($key);
        $this->assertEquals($value, $this->serviceUtils->getParameters()->get($key));
        $this->serviceUtils->getParameters()->remove($key);
        $this->assertEquals(0, $this->serviceUtils->getParameters()->count());
    }

    public function testServices()
    {
        $this->assertEmpty($this->serviceUtils->getServices()->toArray());

        list($id, $service) = $this->getService('KEY');
        $this->serviceUtils->addService($id, $service);
        $this->assertEquals($service, $this->serviceUtils->getServices()->get($id));
        $this->serviceUtils->getServices()->remove($id);
        $this->assertEquals(0, $this->serviceUtils->getServices()->count());
    }

    public function testImports()
    {
        $this->assertEmpty($this->serviceUtils->getImports()->toArray());

        $this->serviceUtils->addImport($this->getImport());
        $this->assertTrue($this->serviceUtils->getImports()->contains($this->getImport()));
        $this->assertEquals(1, $this->serviceUtils->getImports()->count());
        $this->serviceUtils->getImports()->removeElement($this->getImport());
        $this->assertEmpty(0, $this->serviceUtils->getImports()->count());
    }

    public function testFormatConverter()
    {
        $this->assertEquals($this->getFormatConverter(), $this->serviceUtils->getFormatConverter());
    }

    public function testFile()
    {

    }

    public function testYaml()
    {
        list ($key, $value) = $this->getParameter();
        list($id, $service) = $this->getService($key);
        $this->serviceUtils->addParameter($key, $value);
        $this->serviceUtils->addService($id, $service);

        $this->assertEquals(ServiceUtilsData::YAML, $this->serviceUtils->getYaml());
        $this->assertEquals(ServiceUtilsData::XML, $this->serviceUtils->getFormattedContents('yaml'));
        $this->assertEquals(ServiceUtilsData::XML, $this->serviceUtils->getFormattedContents('yml'));
    }

    public function testXml()
    {
        list ($key, $value) = $this->getParameter();
        list($id, $service) = $this->getService($key);
        $this->serviceUtils->addParameter($key, $value);
        $this->serviceUtils->addService($id, $service);

        $this->assertEquals(ServiceUtilsData::XML, $this->serviceUtils->getXml());
        $this->assertEquals(ServiceUtilsData::XML, $this->serviceUtils->getFormattedContents('xml'));
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
