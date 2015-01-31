<?php

namespace Tdn\SfProjectGeneratorBundle\Tests\Model;

use Tdn\SfProjectGeneratorBundle\Manipulator\Object\ParamManipulator;
use Tdn\SfProjectGeneratorBundle\Model\Param;
use Tdn\PhpTypes\Type\String;

/**
 * Class ParamTest
 * @package Tdn\SfProjectGeneratorBundle\Tests\Model
 */
class ParamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Param
     */
    protected $param;

    protected function setUp()
    {
        $rawParam = <<<'PARAM'
SomeInterface $foo = null
PARAM;

        $this->param = new Param(new ParamManipulator(String::create($rawParam)));
    }

    public function testTypeHint()
    {
        $this->assertEquals('SomeInterface', (string) $this->param->getTypeHint());
    }

    public function testVarName()
    {
        $this->assertEquals('foo', (string) $this->param->getVarName());
    }

    public function testDefault()
    {
        $this->assertEquals('null', (string) $this->param->getDefault());
    }

    public function testType()
    {
        $this->assertEquals('SomeInterface', (string) $this->param->getType());
    }

}