<?php

namespace Tdn\SfProjectGeneratorBundle\Manipulator\Object;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\SfProjectGeneratorBundle\Manipulator\BaseManipulator;
use Tdn\SfProjectGeneratorBundle\Model\Param;
use Tdn\PhpTypes\Type\String;

/**
 * Class MethodManipulator
 * @package Tdn\SfProjectGeneratorBundle\Manipulator
 */
class MethodManipulator extends BaseManipulator
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
    protected $function;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $logic;

    /**
     * @param \Tdn\PhpTypes\Type\String $method
     */
    public function __construct(String $method)
    {
        if ($method->contains('/**')) {
            $this->docBlock = $method->subStrUntil('*/');
        }

        /**
         * Doc signature may not match method.
         */
        $method = ($method->contains('/**')) ? $method->subStrAfter('*/', true) : $method;

        switch ($method->subStrUntil('(', true)->trim()->countSubstr(' ')) {
            case 3:
                list($this->scope, $this->modifier,, $this->function) = explode(' ', (string) $method->subStrUntil(')', true)->trim(), 4);
                break;
            case 2:
                list($this->scope,, $this->function) = explode(' ', (string) $method->subStrUntil(')', true)->trim(), 3);
                break;
            default:
                throw new \LogicException('Invalid method format passed. Expected format <phpdoc>*\n<scope> <modifier>* function <name>(<params>*) { ... } * = optional');
        }

        /** @var \Tdn\PhpTypes\Type\String $logic */
        $logic = $method->subStrFromTo('{', '}', true, true)->trim();
        $this->function = String::create($this->function);
        $this->logic = $logic->addSpaces($method->subStrFromTo('{', '}', true, true)->getSpaceSize(), null, true);
        $this->docBlock = ($this->docSignatureMatchesMethod($this->docBlock, $this->findParams())) ? $this->docBlock : null;
    }

    public function findDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function generateDocBlock()
    {
        /** @var \Tdn\PhpTypes\Type\String $docBlock */
        $docBlock = String::create('/**')->ensureRight("\n");

        foreach ($this->findParams() as $param) {
            $docBlock->ensureRight(' * @var ' . (($param->getType()) ? $param->getType() . ' ' : '') . (string) $param->getVarName() . "\n");
        }

        $docBlock->ensureRight(' * @return ' . $this->findType());
        $docBlock->ensureRight(' */');

        return $docBlock->addSpaces(null, null, true);
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findScope()
    {
        return String::create($this->scope);
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findModifier()
    {
        return String::create($this->modifier);
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findName()
    {
        return String::create($this->function)->subStrUntil('(', true);

    }

    /**
     * @return ArrayCollection|Param[]
     */
    public function findParams()
    {
        $possibleParams = $this->function->subStrFromTo('(', ')', true, true);
        $params = new ArrayCollection();

        if (!empty($possibleParams)) {
            $rawParams = explode(', ', (string) $possibleParams);
            foreach ($rawParams as $rawParam) {
                if ($rawParam) {
                    $param = new Param(new ParamManipulator(String::create($rawParam)));
                    $name = $param->getVarName()->removeLeft('$');
                    $params->set((string) $name, $param);
                }
            }
        }

        return $params;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findLogic()
    {
        return $this->logic;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findType()
    {
        $type =  String::create('void');

        if ($this->findLogic()->contains('return')) {
            $type = String::create('mixed');
        }

        return $type;
    }

    /**
     * @todo: super inefficient.
     * @param \Tdn\PhpTypes\Type\String $method
     * @param ArrayCollection $params
     * @return bool
     */
    private function docSignatureMatchesMethod(String $method, ArrayCollection $params)
    {

        /** @var \Tdn\PhpTypes\Type\String $possibleDocBlock */
        $possibleDocBlock = $method->subStrFromTo('/**', '*/', true, true);
        $lines = explode("\n", (string) $possibleDocBlock);
        $lines = array_map(
            function($v) {
                return trim($v);
            },
            $lines
        );
        array_pop($lines);
        array_shift($lines);
        reset($lines);

        if ($params->count() !== count($lines)) {
            return false;
        }

        $params = array_values($params->toArray());
        for ($i = 0; $i < count($lines); $i++) {
            $line = String::create($lines[$i]);
            if($line->contains('* @param') && !$line->countSubstr((string) $params[$i]->getVarName()) > 1) {
                return false;
            }
        }

        return true;
    }
}
