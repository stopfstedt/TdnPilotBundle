<?php

namespace Tdn\SfProjectGeneratorBundle\Manipulator\Object;

use Tdn\SfProjectGeneratorBundle\Manipulator\BaseManipulator;
use Tdn\PhpTypes\Type\String;

/**
 * Class PropertyManipulator
 * @package Tdn\SfProjectGeneratorBundle\Manipulator
 */
class PropertyManipulator extends BaseManipulator
{
    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $docBlock;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $modifier;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $scope;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $defaultValue;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $propertyName;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $type;

    public function __construct(String $property)
    {
        //empty((string) $property)) requires php 5.5+
        if (!mb_strlen((string)$property) > 0) {
            throw new \InvalidArgumentException('Empty string passed to ' . __CLASS__);
        }

        if ($property->contains('/**')) {
            $this->docBlock = ($this->signatureMatchesDocBlock($property)) ? $property->subStrUntil('*/') : null;
        }

        $property = $property->contains('/**') ? $property->subStrAfter('*/', true) : $property;
        $property = $property->trim();

        switch($property->countSubstr(' '))
        {
            //Match examples:
            case 4:
                //protected static $var = 'foo';
                list(
                    $this->scope,
                    $this->modifier,
                    $this->propertyName,
                    ,
                    $this->defaultValue
                    ) = explode(' ', (string) $property);
                break;
            case 3:
                //protected $var = 'foo';
                list($this->scope, $this->propertyName, ,$this->defaultValue) = explode(' ', (string) $property);
                break;
            case 2:
                //protected static $var;
                list($this->scope, $this->modifier, $this->propertyName) = explode(' ', (string) $property);
                break;
            case 1:
            default:
                //protected $var;
                list($this->scope, $this->propertyName) = explode(' ', (string) $property);
                break;
        }

    }

    /**
     * @return \Tdn\PhpTypes\Type\String|null
     */
    public function findDocBlock()
    {
        $counter = $index = 0;
        foreach ($this->docBlock as $char) {
            if ($char !== ' ') {
                continue;
            }

            if ((array_key_exists($this->docBlock[$index + 1], (array) $this->docBlock) &&
                $this->docBlock[$index + 1] === ' ') && $char === ' ') {
                $counter++;
            }

            if ((!array_key_exists($this->docBlock[$index + 1], (array) $this->docBlock) &&
                $this->docBlock[$index - 1] == ' ') && $char == ' ') {
                $counter++;
            }

            if ($char !== ' ' && $this->docBlock[$index - 1] == ' ') {
                $counter++;
            }
        }

        return ($this->docBlock) ? String::create($this->docBlock)->trim()->addSpaces($counter) : null;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findScope()
    {
        return String::create($this->scope)->trim();
    }

    /**
     * @return \Tdn\PhpTypes\Type\String|null
     */
    public function findModifier()
    {
        return ($this->modifier) ? String::create($this->modifier)->trim() : null;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findPropertyName()
    {
        return String::create($this->propertyName)->removeLeft('$');
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findDefault()
    {
        return ($this->defaultValue) ? String::create($this->defaultValue)->removeRight(';') : null;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findType()
    {

    }

    /**
     * @todo: implement
     * @param $method
     * @return bool
     */
    protected function signatureMatchesDocBlock($method)
    {
        return true;
    }
}
