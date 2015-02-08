<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;

/**
 * Class GenerateManagerCommand
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
    const DESCRIPTION = 'tdn:generate:manager';

    /**
     * @param InputInterface          $input
     * @param OutputEngineInterface   $outputEngine
     * @param BundleInterface         $bundle
     * @param ClassMetadataInfo       $metadata
     *
     * @return ManagerManipulator
     */
    protected function createManipulator(
        InputInterface $input,
        OutputEngineInterface $outputEngine,
        BundleInterface $bundle,
        ClassMetadataInfo $metadata
    ) {
        return new ManagerManipulator($outputEngine, $bundle, $metadata);
    }

    /**
     * @return string
     */
    protected function getFileType()
    {
        return 'Entity Manager';
    }
}
