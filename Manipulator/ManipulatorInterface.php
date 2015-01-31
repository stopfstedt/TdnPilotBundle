<?php

namespace Tdn\SfProjectGeneratorBundle\Manipulator;

use Tdn\SfProjectGeneratorBundle\Model\Source;

interface ManipulatorInterface
{
    /**
     * @param Source $source
     */
    public function setSource(Source $source);

    /**
     * @return Source
     */
    public function getSource();
}