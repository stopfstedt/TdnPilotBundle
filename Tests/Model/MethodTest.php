<?php

namespace Tdn\SfProjectGeneratorBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\PhpManipulator\TokenStream;

use Tdn\SfProjectGeneratorBundle\Manipulator\Object\MethodManipulator;
use Tdn\SfProjectGeneratorBundle\Manipulator\Object\ParamManipulator;
use Tdn\SfProjectGeneratorBundle\Model\Method;
use Tdn\SfProjectGeneratorBundle\Model\Param;
use Tdn\PhpTypes\Type\String;

/**
 * Class MethodTest
 * @package Tdn\SfProjectGeneratorBundle\Tests\Model
 */
class MethodTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMethod = <<<'RAW'
    /**
     * @param string $testProp
     * @param Interface2 $testProp2
     * @param array $testProp3
     */
    public static function setTestProp($testProp, Interface2 $testProp2, array $testProp3 = [])
    {
        $this->testProp = $testProp . (string) $testProp2 . implode(', ', $testProp3);
    }
RAW;

    /**
     * @var Method
     */
    protected $method;

    protected function setUp()
    {
        $this->method = new Method(new MethodManipulator(String::create(self::$rawMethod)));
    }

    public function testDocBlock()
    {
        $docBlock = <<<'DOCBLOCK'
    /**
     * @param string $testProp
     * @param Interface2 $testProp2
     * @param array $testProp3
     */
DOCBLOCK;
        $this->assertEquals($docBlock, (string) $this->method->getDocBlock()->addSpaces());
    }

    public function testScope()
    {
        $this->assertEquals('public', (string) $this->method->getScope());
    }

    public function testModifier()
    {
        $this->assertEquals('static', (string) $this->method->getModifier());
    }

    public function testName()
    {
        $this->assertEquals('setTestProp', (string) $this->method->getName());
    }

    public function testParams()
    {
        $rawParams = explode(", ", '$testProp, Interface2 $testProp2, array $testProp3 = []');
        $params = new ArrayCollection();

        foreach ($rawParams as $rParam) {
            $param = new Param(new ParamManipulator(String::create($rParam)));
            $name = $param->getVarName()->removeLeft('$');
            $params->set((string) $name, $param);
        }

        $this->assertEquals($params, $this->method->getParams());
    }

    public function testLogic()
    {
        $logic = <<<'LOGIC'
        $this->testProp = $testProp . (string) $testProp2 . implode(', ', $testProp3);
LOGIC;
        $this->assertEquals($logic, (string) $this->method->getLogic());
    }

    public function testType()
    {
        $this->assertEquals('void', (string) $this->method->getType());
    }
}