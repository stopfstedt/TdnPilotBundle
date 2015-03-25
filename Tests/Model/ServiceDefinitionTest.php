<?php

namespace Tdn\PilotBundle\Tests\Model;

use Symfony\Component\DependencyInjection\Definition;
use Tdn\PilotBundle\Model\ServiceDefinition;

/**
 * Class ServiceDefinitionTest
 * @package Tdn\PilotBundle\Tests\Model
 */
class ServiceDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceDefinition
     */
    protected $serviceDefinition;

    /**
     * @var Definition
     */
    protected $definition;

    protected function setUp()
    {
        $this->definition = new Definition('%test.class%');
        $this->serviceDefinition = new ServiceDefinition('test.id', $this->definition);
    }

    public function testValues()
    {
        $this->assertEquals('test.id', $this->serviceDefinition->getId());
        $this->assertEquals($this->definition, $this->serviceDefinition->getDefinition());
    }
}
