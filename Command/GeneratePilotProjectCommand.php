<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;

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
     * @throws \RuntimeException Not needed in this sub type.
     *
     * @return void
     */
    protected function createManipulator()
    {
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
     * @return ArrayCollection|ManipulatorInterface[]
     */
    protected static function getSupportedManipulators()
    {
        return new ArrayCollection([
            new ManagerManipulator(),
        ]);
    }
}
