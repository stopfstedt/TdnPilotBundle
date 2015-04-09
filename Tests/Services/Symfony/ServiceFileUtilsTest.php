<?php

namespace Tdn\PilotBundle\Tests\Services\Symfony;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Model\Format;
use Tdn\PilotBundle\Model\ServiceDefinition;
use Tdn\PilotBundle\Services\Utils\Symfony\ServiceFileUtils;
use Tdn\PilotBundle\Tests\Fixtures\ServiceFileUtilsData;

class ServiceFileUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceFileUtils
     */
    protected $serviceFileUtils;

    protected function setUp()
    {
        $this->serviceFileUtils = new ServiceFileUtils();
    }

    public function testSupportedExtensions()
    {
        $this->assertContains(Format::YML, ServiceFileUtils::getSupportedExtensions());
        $this->assertContains(Format::YAML, ServiceFileUtils::getSupportedExtensions());
        $this->assertContains(Format::XML, ServiceFileUtils::getSupportedExtensions());
        $this->assertNotContains(Format::ANNOTATION, ServiceFileUtils::getSupportedExtensions());
    }

    public function testServiceAsXml()
    {
        $this->dumpToFormat(ServiceFileUtilsData::XML, 'tmp-service.xml');
    }

    public function testSeededXml()
    {

    }

    public function testServiceAsYaml()
    {
        $this->dumpToFormat(ServiceFileUtilsData::YAML, 'tmp-service.yaml');
    }

    public function testSeededYaml()
    {

    }

    /**
     * @return ServiceDefinition
     */
    protected function getServiceDefinition()
    {
        $definition = new Definition('%foo_bar.manager.foo_manager.class%');
        $definition
            ->addArgument(new Reference('doctrine'))
            ->addArgument('Foo\BarBundle\Entity\Foo')
        ;

        return new ServiceDefinition('foo_bar.manager.foo_manager', $definition);
    }

    /**
     * @param string $fileName
     * @param string $format
     */
    protected function dumpToFormat($format, $fileName)
    {
        $file = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName);
        $this->serviceFileUtils->addParameter(
            'foo_bar.manager.foo_manager.class',
            'Foo\BarBundle\Entity\Manager\FooManager'
        );


        $this->serviceFileUtils->addServiceDefinition($this->getServiceDefinition());
        $this->assertEquals($format, $this->serviceFileUtils->dump($file));
    }
}
