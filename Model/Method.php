<?php

namespace Tdn\SfProjectGeneratorBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Tdn\SfProjectGeneratorBundle\Manipulator\Object\MethodManipulator;
use Tdn\PhpTypes\Type\String;

/**
 * Class Method
 * @package Tdn\SfProjectGeneratorBundle\Services\AST
 */
class Method
{
    /**
     * @var MethodManipulator
     */
    protected $manipulator;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $docBlock;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $scope;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $modifier;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $name;

    /**
     * @var ArrayCollection
     */
    protected $params;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $logic;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $type;

    /**
     * @return array
     */
    public static function getScopes()
    {
        return [
            'public',
            'protected',
            'private'
        ];
    }

    public function __construct(MethodManipulator $manipulator)
    {
        $this->manipulator = $manipulator;
        $this->docBlock = ($manipulator->findDocBlock() !== null) ? $manipulator->findDocBlock()->trim() : $manipulator->generateDocBlock()->trim();
        $this->scope = $manipulator->findScope();
        $this->modifier = $manipulator->findModifier();
        $this->name = $manipulator->findName();
        $this->params = $manipulator->findParams();
        $this->logic = $manipulator->findLogic();
        $this->type = $manipulator->findType();
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $docBlock
     */
    public function setDocBlock(String $docBlock)
    {
        $this->docBlock = $docBlock;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $scope
     */
    public function setScope(String $scope)
    {
        $this->scope = $scope;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $modifier
     */
    public function setModifier(String $modifier)
    {
        $this->modifier = $modifier;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $name
     */
    public function setName(String $name)
    {
        $this->name = $name;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Collection $params
     */
    public function setParams(Collection $params)
    {
        $this->params = new ArrayCollection();
        foreach ($params as $param) {
            $this->addParam($param);
        }
    }

    /**
     * @param Param $param
     */
    public function addParam(Param $param)
    {
        $this->params->add($param);
    }

    /**
     * @return ArrayCollection|Param[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $logic
     */
    public function setLogic(String $logic)
    {
        $this->logic = $logic;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $type
     */
    public function setType(String $type)
    {
        $this->type = $type;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getType()
    {
        return $this->type;
    }
}