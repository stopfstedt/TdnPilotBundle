<?php

namespace Tdn\SfProjectGeneratorBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tdn\PhpTypes\Type\String;

use Tdn\SfProjectGeneratorBundle\Manipulator\ObjectManipulator;

/**
 * Class Object
 * @package Tdn\SfProjectGeneratorBundle\Services\AST
 */
class Object
{
    const TYPE_UNKNOWN = -999;

    /**
     * @var ObjectManipulator
     */
    protected $manipulator;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $namespace;

    /**
     * @var ArrayCollection
     */
    protected $useLines;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $docBlock;

    /**
     * @var \Tdn\PhpTypes\Type\String
     */
    protected $name;

    /**
     * @var ArrayCollection|Object[]
     */
    protected $interfaces;

    /**
     * @var ArrayCollection|Object[]
     */
    protected $traits;

    /**
     * @var ArrayCollection|Property[]
     */
    protected $properties;

    /**
     * @var ArrayCollection|Method[]
     */
    protected $methods;

    /**
     * @return array
     */
    public static function getValidTypes()
    {
        return [
            T_INTERFACE,
            T_ABSTRACT,
            T_CLASS,
            T_TRAIT
        ];
    }

    public function __construct(ObjectManipulator $manipulator)
    {
        $this->manipulator = $manipulator;
        $this->namespace = $manipulator->findNamespace();
        $this->useLines = $manipulator->findUseLines();
        $this->docBlock = $manipulator->findDocBlock();
        $this->name = $manipulator->findName();
        $this->interfaces = $manipulator->findInterfaces();
        $this->traits = $manipulator->findTraits();
        $this->properties = $manipulator->findProperties();
        $this->methods = $manipulator->findMethods();
        $this->type = $manipulator->findType();

        if (!$this->isValid()) {
            throw new \RuntimeException('Object is not valid.');
        }

    }

    /**
     * @param \Tdn\PhpTypes\Type\String $namespace
     */
    public function setNamespace(String $namespace)
    {
        $this->namespace = $namespace->ensureLeft('namespace ')->ensureRight(';');
    }

    /**
     * @return \Tdn\PhpTypes\Type\String|null
     */
    public function getNamespace()
    {
        return ($this->namespace === null) ? null : $this->namespace->removeLeft('namespace ')->removeRight(';');
    }

    /**
     * @param Collection $useLines
     */
    public function setUseLines(Collection $useLines)
    {
        $this->useLines = new ArrayCollection();
        foreach ($useLines as $useLine) {
            $this->addUseLine($useLine);
        }
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $useLine
     */
    public function addUseLine(String $useLine)
    {
        if (!$this->useLines->contains($useLine)) {
            if (!$useLine->startsWith('use ') || !$useLine->endsWith(';')) {
                $useLine = $useLine->ensureLeft('use ')->ensureRight(';');
            }

            $this->useLines->add($useLine);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getUseLines()
    {
        if (!$this->useLines == null) {
            return $this->useLines->map(function (String $v) {
                return $v->removeLeft('use ')->removeRight(';');
            });
        }

        return $this->useLines;
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
     * @param Collection $interfaces
     */
    public function setInterfaces(Collection $interfaces)
    {
        $this->interfaces = new ArrayCollection();
        foreach ($interfaces as $interface) {
            $this->addInterface($interface);
        }
    }

    /**
     * @param self $interface
     */
    public function addInterface(Object $interface)
    {
        $this->interfaces->add($interface);
    }

    /**
     * @return ArrayCollection|self[]
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @param Collection $traits
     */
    public function setTraits(Collection $traits)
    {
        $this->traits = new ArrayCollection();
        foreach ($traits as $trait) {
            $this->addTrait($trait);
        }
    }

    /**
     * @param self $trait
     */
    public function addTrait(Object $trait)
    {
        $this->traits->add($trait);
    }

    /**
     * @return ArrayCollection|self[]
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * @param Collection $properties
     */
    public function setProperties(Collection $properties)
    {
        $this->properties = new ArrayCollection();
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    /**
     * @param Property $property
     */
    public function addProperty(Property $property)
    {
        $this->properties->add($property);
    }

    /**
     * @return ArrayCollection|Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param Collection $methods
     */
    public function setMethods(Collection $methods)
    {
        $this->methods = new ArrayCollection();
        foreach ($methods as $method) {
            $this->addMethod($method);
        }
    }

    /**
     * @param Method $method
     */
    public function addMethod(Method $method)
    {
        $this->methods->add($method);
    }

    /**
     * @return ArrayCollection|Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return ObjectManipulator
     */
    public function getManipulator()
    {
        return $this->manipulator;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        if (!in_array($type, self::getValidTypes())) {
            throw new \RuntimeException('Invalid type for object ' . $type);
        }

        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    private function isValid()
    {
        if (in_array($this->getType(), self::getValidTypes()) &&
            $this->manipulator->getSource()->getInternalTokens()->contains('T_NAMESPACE')
        ) {
            return true;
        }

        return false;
    }
}