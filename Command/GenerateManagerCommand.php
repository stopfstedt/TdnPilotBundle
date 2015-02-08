<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Class GenerateManagerCommand
 *
 * Generates a entity manager specific for an entity (DAO)
 * with a repository as a dependency.
 *
 * @package Tdn\PilotBundle\Command
 */
class GenerateManagerCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'tdn:generate:manager';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates an entity manager (Repository + DAO patterns) for a given entity.';

    /**
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     *
     * @return ManagerManipulator
     */
    protected function createManipulator(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        return new ManagerManipulator($templateStrategy, $bundle, $metadata);
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Entity Manager', 'Entity Manager Interface'];
    }
}
