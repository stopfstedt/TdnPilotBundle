<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;

/**
 * Class GenerateHandlerCommand
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
     * @param InputInterface          $input
     * @param OutputEngineInterface   $outputEngine
     * @param BundleInterface         $bundle
     * @param ClassMetadataInfo       $metadata
     *
     * @return HandlerManipulator
     */
    protected function createManipulator(
        InputInterface $input,
        OutputEngineInterface $outputEngine,
        BundleInterface $bundle,
        ClassMetadataInfo $metadata
    ) {
        return new HandlerManipulator($outputEngine, $bundle, $metadata);
    }

    /**
     * @return string
     */
    protected function getFileType()
    {
        return 'Rest Handler';
    }
}
