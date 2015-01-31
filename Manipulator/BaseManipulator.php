<?php

namespace Tdn\SfProjectGeneratorBundle\Manipulator;

use Tdn\SfProjectGeneratorBundle\Model\Source;

/**
 * Class BaseManipulator
 * @package Tdn\SfProjectGeneratorBundle\Manipulator
 */
class BaseManipulator implements ManipulatorInterface
{
    protected $source;
    /**
     * @param Source $source
     */
    public function setSource(Source $source)
    {
        $this->source = $source;
    }

    /**
     * @return Source
     */
    public function getSource()
    {
        return $this->source;
    }

}