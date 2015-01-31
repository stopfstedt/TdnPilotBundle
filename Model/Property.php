<?php

namespace Tdn\SfProjectGeneratorBundle\Model;

use Tdn\PhpTypes\Type\String;
use Tdn\SfProjectGeneratorBundle\Manipulator\Object\PropertyManipulator;

/**
 * Class Property
 * @package Tdn\SfProjectGeneratorBundle\Model
 */
class Property
{
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
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $default;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $type;

    /**
     * @param PropertyManipulator $manipulator
     */
    public function __construct(PropertyManipulator $manipulator)
    {
        $this->docBlock = $manipulator->findDocBlock();
        $this->scope = $manipulator->findScope();
        $this->modifier = $manipulator->findModifier();
        $this->name = $manipulator->findPropertyName();
        $this->default = $manipulator->findDefault();
        $this->type = $manipulator->findType();
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $dockBlock
     */
    public function setDocBlock(String $dockBlock)
    {
        $this->docBlock = $dockBlock;
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
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getDefaultType()
    {
        return String::create(gettype($this->default));
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

    public function __toString()
    {
        return (string) $this->name;
    }
}
