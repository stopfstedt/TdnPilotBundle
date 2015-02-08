<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Manipulator\FormManipulator;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Class GenerateFormCommand
 *
 * Generates a form type class for a given Doctrine entity, with optional REST generator support.
 *
 * @package Tdn\PilotBundle\Command
 */
class GenerateFormCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'tdn:generate:form';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates a form type class based on a doctrine entity.';

    /**
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     *
     * @return FormManipulator
     */
    protected function createManipulator(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        return new FormManipulator($templateStrategy, $bundle, $metadata);
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Form type'];
    }
}
