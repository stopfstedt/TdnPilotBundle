<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Tdn\PilotBundle\Manipulator\HandlerManipulator;

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
     * @return HandlerManipulator
     */
    protected function createManipulator()
    {
        return new HandlerManipulator();
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Rest Handler'];
    }
}
