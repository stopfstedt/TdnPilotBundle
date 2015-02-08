<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Class GenerateHandlerCommand
 *
 * Generates a REST handler that provide a way of managing your entities in a controller context.
 *
 * @package Tdn\PilotBundle\Command
 */
class GenerateHandlerCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'tdn:generate:handler';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates an entity REST handler file for a controller.';

    /**
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     *
     * @return HandlerManipulator
     */
    protected function createManipulator(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        return new HandlerManipulator($templateStrategy, $bundle, $metadata);
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Rest Handler'];
    }
}
