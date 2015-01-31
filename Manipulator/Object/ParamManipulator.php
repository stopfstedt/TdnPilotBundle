<?php

namespace Tdn\SfProjectGeneratorBundle\Manipulator\Object;

use Tdn\SfProjectGeneratorBundle\Manipulator\BaseManipulator;
use Tdn\PhpTypes\Type\String;

/**
 * Class ParamManipulator
 * @package Tdn\SfProjectGeneratorBundle\Manipulator
 */
class ParamManipulator extends BaseManipulator
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

    public function __construct(String $param)
    {
        if (!mb_strlen((string)$param) > 0) {
            throw new \InvalidArgumentException('Empty string passed to ' . __CLASS__);
        }

        // We have "TypeHint $var = default" or some variation of that.
        if (!$param->trim()->startsWith('$') && $param->length() > 0) {
            list($this->typeHint, $rest) = explode(' ', (string) $param->trim(), 2);
            $param = String::create($rest);
        }

        //We have a default...
        if ($param->contains('=')) {
            list($this->varName, $this->default) = explode('=', (string) $param->trim());
        } else {
            $this->varName = (string) $param->trim();
        }
    }

    /**
     * @return \Tdn\PhpTypes\Type\String|null
     */
    public function findTypeHint()
    {
        return ($this->typeHint !== null) ? String::create($this->typeHint)->trim() : null;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findVarName()
    {
        return String::create($this->varName)->removeLeft('$')->trim();
    }

    /**
     * @return \Tdn\PhpTypes\Type\String|null
     */
    public function findDefault()
    {
        return ($this->default !== null) ? String::create($this->default)->trim() : null;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findType()
    {
        $result = $this->findTypeHint();

        if (!$result && null !== $default = $this->findDefault()) {
            switch(true) {
                //@todo: Find way to detect constant values??
                case ($default->isUpperCase() && $default->first(1) != '\'' && $default->first(1) != '"'):
                    $result = (defined(constant((string) $default))) ? gettype(constant((string) $default)) : 'const';
                    break;
                case (is_numeric($this->default)):
                    $result = 'int';
                    break;
                case ($default->toLowerCase() == 'true' || $default->toLowerCase() == 'false'):
                    $result =  'bool';
                    break;
                case ($default->first(1) == '[' || $default->toLowerCase()->contains('array')):
                    $result =  'array';
                    break;
                default:
                    $result =  'string';
                    break;
            }
        }

        return String::create((string) $result);
    }
}
