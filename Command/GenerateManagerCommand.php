<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tdn\PilotBundle\Manipulator\ManagerManipulator;

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
     * @param InputInterface $input
     *
     * @return ManagerManipulator
     */
    protected function createManipulator(InputInterface $input)
    {
        return new ManagerManipulator();
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Entity Manager', 'Entity Manager Interface'];
    }
}
