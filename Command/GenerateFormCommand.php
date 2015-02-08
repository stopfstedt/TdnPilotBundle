<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PilotBundle\Manipulator\FormManipulator;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;

/**
 * Generates a form type class for a given Doctrine entity, with optional REST generator support.
 *
 * @author Victor Passapera <vpassapera@gmail.com>
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
     * @return InputOption[]
     */
    protected function getInputArgs()
    {
        return [
            new InputOption(
                'rest-support',
                'r',
                InputOption::VALUE_NONE,
                'Generate an form type with tdn_entity support'
            )
        ];
    }

    /**
     * @param InputInterface          $input
     * @param OutputEngineInterface   $outputEngine
     * @param BundleInterface         $bundle
     * @param ClassMetadataInfo       $metadata
     *
     * @return FormManipulator
     */
    protected function createManipulator(
        InputInterface $input,
        OutputEngineInterface $outputEngine,
        BundleInterface $bundle,
        ClassMetadataInfo $metadata
    ) {
        $manipulator = new FormManipulator($outputEngine, $bundle, $metadata);
        $manipulator->setRestSupport(($input->getOption('rest-support') ? true : false));

        return $manipulator;
    }

    /**
     * @return string
     */
    protected function getFileType()
    {
        return 'Form type';
    }
}
