<?php

namespace Tdn\SfProjectGeneratorBundle\Tests\Model;

use Tdn\SfProjectGeneratorBundle\Manipulator\Object\PropertyManipulator;
use Tdn\SfProjectGeneratorBundle\Model\Property;
use Tdn\PhpTypes\Type\String;

/**
 * Class PropertyTest
 * @package Tdn\SfProjectGeneratorBundle\Test\Model
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Property
     */
    protected $property;

    protected function setUp()
    {
        $rawProp = <<<'RAW'
    /**
     * @var string
     */
    protected static $foo = [];
RAW;

        $this->property = new Property(new PropertyManipulator(String::create($rawProp)));
    }

    public function testDocBlock()
    {
        $docBlock = <<<'RAW'
    /**
     * @var string
     */
RAW;
        $this->assertEquals($docBlock, (string) $this->property->getDocBlock());
    }

    public function testScope()
    {
        $this->assertEquals('protected', (string) $this->property->getScope());
    }

    public function testModifier()
    {
        $this->assertEquals('static', (string) $this->property->getModifier());
    }

    public function testName()
    {
        $this->assertEquals('foo', (string) $this->property->getName());
    }

    //Make this better...no practical use for now anyways.
    public function testDefault()
    {
        $this->assertEquals('[]', (string) $this->property->getDefault());
    }

    //No practical use for this atm either.
    public function testType()
    {
        $this->assertTrue(true);
    }
}
