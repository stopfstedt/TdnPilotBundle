<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Class GeneratePilotProjectCommand
 *
 * Generates a pilot project from your entities.
 *
 * @package Tdn\PilotBundle\Command
 */
class GeneratePilotProjectCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'tdn:generate:pilot';

    /**
     * @var string
     */
    const DESCRIPTION = 'Creates all project files based on specified entity/entities.';

    /**
     * The magic
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Creates a new manipulator instance
     *
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     *
     * @throws \RuntimeException Not needed in this sub type.
     *
     * @return void
     */
    protected function createManipulator(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        throw new \RuntimeException('Not implemented in ' . get_called_class());
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return [];
    }

    /**
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface $bundle
     * @param ClassMetadata $metadata
     *
     * @return ArrayCollection|ManipulatorInterface[]
     */
    protected static function getSupportedManipulators(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        return new ArrayCollection([
            new ManagerManipulator($templateStrategy, $bundle, $metadata),
        ]);
    }
}
