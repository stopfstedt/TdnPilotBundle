<?php

namespace Tdn\PilotBundle\Command;

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
     * @return InputOption[]
     */
    protected function getInputArgs()
    {
        return [
            new InputOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'The service file format (yaml, xml)',
                'yaml'
            )
        ];
    }

    /**
     * @return ManagerManipulator
     */
    protected function createManipulator()
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
