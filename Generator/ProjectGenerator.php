<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class ProjectGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class ProjectGenerator extends Generator
{
    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param ArrayCollection   $options
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, ArrayCollection $options = null)
    {
        foreach ($this->getGenerators() as $generator) {
            $generator->generate($bundle, $entity, $metadata, $options);
        }
    }

    /**
     * @return ArrayCollection|GeneratorInterface[]
     */
    protected function getGenerators()
    {
        return new ArrayCollection([
            new FormGenerator(),
            new ManagerGenerator(),
            new HandlerGenerator(),
            new ControllerGenerator(),
            new RoutingGenerator()
        ]);
    }
}
