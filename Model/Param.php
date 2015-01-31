<?php

namespace Tdn\SfProjectGeneratorBundle\Model;

use Tdn\SfProjectGeneratorBundle\Manipulator\Object\ParamManipulator;
use Tdn\PhpTypes\Type\String;

/**
 * Class Param
 * @package Tdn\SfProjectGeneratorBundle\Model
 */
class Param
{
    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $typeHint;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $varName;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $default;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $type;

    public function __construct(ParamManipulator $manipulator)
    {
        $this->typeHint = $manipulator->findTypeHint();
        $this->varName = $manipulator->findVarName();
        $this->default = $manipulator->findDefault();
        $this->type = $manipulator->findType();
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $typeHint
     */
    public function setTypeHint(String $typeHint)
    {
        $this->typeHint = $typeHint;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getTypeHint()
    {
        return $this->typeHint;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $varName
     */
    public function setVarName(String $varName)
    {
        $this->varName = $varName;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getVarName()
    {
        return $this->varName;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $default
     */
    public function setDefault(String $default)
    {
        $this->default = $default;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function getType()
    {
        return $this->type;
    }
}