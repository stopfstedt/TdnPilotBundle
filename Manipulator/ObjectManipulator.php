<?php

namespace Tdn\SfProjectGeneratorBundle\Manipulator;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\PhpManipulator\TokenStream\PhpToken;
use JMS\PhpManipulator\TokenStream\LiteralToken;
use Tdn\PhpTypes\Type\String;

use Tdn\SfProjectGeneratorBundle\Manipulator\Object\MethodManipulator;
use Tdn\SfProjectGeneratorBundle\Manipulator\Object\PropertyManipulator;
use Tdn\SfProjectGeneratorBundle\Model\Property;
use Tdn\SfProjectGeneratorBundle\Model\Object;
use Tdn\SfProjectGeneratorBundle\Model\Source;
use Tdn\SfProjectGeneratorBundle\Model\Method;

/**
 * Class ObjectManipulator
 * @package Tdn\SfProjectGeneratorBundle\Manipulator
 */
class ObjectManipulator extends BaseManipulator
{
    /**
     * @var array
     */
    protected static $tokenMap = [
        "T_CLASS" => T_CLASS,
        "T_ABSTRACT" => T_ABSTRACT,
        "T_INTERFACE" => T_INTERFACE,
        "T_TRAIT" => T_TRAIT
    ];

    /**
     * @var array
     */
    protected static $scopeTokens = [
        'public' => T_PUBLIC,
        'protected' => T_PROTECTED,
        'private' => T_PRIVATE
    ];

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function findType()
    {
        $result = new ArrayCollection(array_intersect(
            array_keys(self::$tokenMap),
            $this->source->getInternalTokens()->toArray()
        ));

        if ($result->count() > 1 || $result->count() <= 0) {
            $type = Object::TYPE_UNKNOWN;
        } else {
            $type = self::$tokenMap[$result->first()];
        }

        return $type;
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findNamespace()
    {
        while ($this->source->getStream()->moveNext()) {
            if ($this->source->getStream()->token->matches(T_NAMESPACE)) {
                return String::create(
                    $this->source->getStream()->getLineContent($this->source->getStream()->token->getLine())
                )->trim()
                    ->removeLeft('namespace ')
                    ->removeRight(';');
            }
        }
        $this->source->getStream()->reset();
    }

    /**
     * @return ArrayCollection
     */
    public function findUseLines()
    {
        return $this->findUseLinesSwitch(false);
    }

    /**
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findDocBlock()
    {
        $docBlock = '';
        while ($this->source->getStream()->moveNext()) {
            if ($this->source->getStream()->token->isComment() &&
                $this->source->getStream()->token->getNextToken()->get()->matches($this->findType())
            ) {
                $docBlock = $this->source->getStream()->token->getContent();
                break;
            }
        }
        $this->source->getStream()->reset();

        return String::create($docBlock);
    }

    /**
     * @param bool $useFile
     *
     * @return \Tdn\PhpTypes\Type\String
     */
    public function findName($useFile = false)
    {
        if ($useFile) {
            $nameToken = $this->source->getFile()->getBasename('.php');
        } else {
            $nameToken = '';
            while ($this->source->getStream()->moveNext()) {
                if ($this->source->getStream()->token->matches($this->findType())) {
                    /** @var LiteralToken $nameToken */
                    $nameToken = $this->source->getStream()->token->getNextToken()->get()->getValue();
                    break;
                }
            }
            $this->source->getStream()->reset();
        }

        return String::create($nameToken);
    }

    /**
     * @return ArrayCollection
     */
    public function findInterfaces()
    {
        $interfaces = new ArrayCollection();
        $tokens     = $this->source->getTokens(true);

        //Only run if Implements its located somewhere in the content of tokens (which are arrays)
        if (in_array(
            'Implements',
            $tokens->map(
                function ($arr) {
                    return ucfirst($arr['token']);
                }
            )->toArray()
        )) {
            $line = null;
            foreach ($tokens as $tokenArray) {
                if (ucfirst($tokenArray['token']) == 'Implements') {
                    $line = $tokenArray['line'];
                    break;
                }
            }
            /** @var \Tdn\PhpTypes\Type\String $lineWithInterfaces */
            $lineWithInterfaces = String::create($this->source->getStream()->getLineContent($line));
            $interfaceString = $lineWithInterfaces->subStrAfter('implements', true)->trim();
            $interfaces = new ArrayCollection(explode(', ', (string) $interfaceString));
        }

        return $interfaces;
    }

    /**
     * @return ArrayCollection
     */
    public function findTraits()
    {
        return $this->findUseLinesSwitch(true);
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $scope
     * @return ArrayCollection|Property[]
     */
    public function findProperties(String $scope = null)
    {
        $properties = new ArrayCollection();
        $match = ($scope) ? $this->scopeToToken($scope) : T_VARIABLE;
        $passedClassMarker = false;
        $passedMethodsMarker = false;
        $varMatcher = null;
        while ($this->source->getStream()->moveNext()) {
            $passedClassMarker = ($this->source->getStream()->token->matches(T_CLASS)) ? true : $passedClassMarker;
            $passedMethodsMarker = ($this->source->getStream()->token->matches(T_FUNCTION)) ? true : $passedMethodsMarker;
            // After T_CLASS
            //$this->source->getStream()->token->matches(T_CONST) needs to be implemented as findConstants();
            if ($passedClassMarker && ($this->source->getStream()->token->matches($match) && !$passedMethodsMarker)){
                //We have a property.
                /** @var PhpToken $docBlock */
                $rawProperty = String::create($this->source->getStream()->getLineContent($this->source->getStream()->token->getLine()));
                //Find DocComment
                $docBlock = $this->source->getStream()->token->findPreviousToken(T_DOC_COMMENT)->get();

                if ($docBlock->getContent() !== null) {
                    $rawProperty = $rawProperty->ensureLeft("\n")->ensureLeft($docBlock->getContent());
                }

                $property = new Property(new PropertyManipulator($rawProperty));
                $properties->set((string) $property->getName(), $property);
            }
        }
        $this->source->getStream()->reset();

        return $properties;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $scope
     *
     * @return ArrayCollection|Method[]
     */
    public function findMethods(String $scope = null)
    {
        $this->source->getStream()->reset();
        $methods = new ArrayCollection();
        $match = ($scope) ? $this->scopeToToken($scope) : T_FUNCTION;
        while ($this->source->getStream()->moveNext()) {
            if ($this->source->getStream()->token->matches($match)) {
                /** @var \Tdn\PhpTypes\Type\String $rawLine */
                $line = String::create($this->source->getStream()->getLineContent($this->source->getStream()->token->getLine()));
                if ($line->contains('function', false) && $line->contains('(')) {
                    /** @var \Tdn\PhpTypes\Type\String $source */
                    $source = String::create($this->source->getFile()->getContents());
                    /** @var PhpToken $docBlock */
                    $docBlock = $this->source->getStream()->token->findPreviousToken(T_DOC_COMMENT)->get();
                    /** @var \Tdn\PhpTypes\Type\String $rawMethod */
                    $rawMethod = $source->subStrFromTo((string) $line, '}')->ensureLeft(str_repeat(' ', $line->getSpaceSize()));

                    if ($docBlock->getContent() !== null) {
                        $rawMethod = $rawMethod->ensureLeft("\n")->ensureLeft($docBlock->getContent())->ensureLeft(str_repeat(' ', $line->getSpaceSize()));
                    }

                    $method = new Method(new MethodManipulator($rawMethod));
                    $methods->set((string) $method->getName()->trim(), $method);
                }
            }
        }
        $this->source->getStream()->reset();

        return $methods;
    }

    /**
     * @param bool $pastClassMarker
     *
     * @return ArrayCollection
     */
    private function findUseLinesSwitch($pastClassMarker = false)
    {
        $useLines = new ArrayCollection();
        $passedClassMarker = false;
        while ($this->source->getStream()->moveNext()) {
            $passedClassMarker = ($this->source->getStream()->token->matches(T_CLASS)) ? true : $passedClassMarker;

            if (($pastClassMarker && $passedClassMarker) || (!$pastClassMarker && !$passedClassMarker)) {
                if ($this->source->getStream()->token->matches(T_USE)) {
                    $useLine = String::create(
                        $this->source->getStream()->getLineContent($this->source->getStream()->token->getLine())
                    )->trim()
                        ->removeLeft('use ')
                        ->removeRight(';');
                    $useLines->add($useLine);
                }
            }
        }
        $this->source->getStream()->reset();

        return $useLines;
    }

    /**
     * @param \Tdn\PhpTypes\Type\String $scope
     * @throws \Exception
     *
     * @return int T_PUBLIC|T_PROTECTED|T_PRIVATE
     */
    private function scopeToToken(String $scope)
    {
        return self::$scopeTokens[(string) $scope->trim()];
    }
}
