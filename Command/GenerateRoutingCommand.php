<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Tdn\PilotBundle\Manipulator\RoutingManipulator;

/**
 * Class GenerateRoutingCommand
 *
 * Adds / Removes routes from the routing file based on an entity.
 *
 * @package Tdn\SfRoutingGeneratorBundle\Command
 */
class GenerateRoutingCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const DEFAULT_ROUTING = 'routing';

    /**
     * @var string
     */
    const NAME = 'tdn:generate:routing';

    /**
     * @var string
     */
    const DESCRIPTION =
        'Adds a routing entry for a rest controller based on an entity. Removes it with the --remove flag.';

    /**
     * @return array<InputArgument|InputOption>
     */
    protected function getInputArgs()
    {
        return [
            new InputArgument(
                'routing-file',
                InputArgument::OPTIONAL,
                'The routing file, defaults to: ' . self::DEFAULT_ROUTING,
                self::DEFAULT_ROUTING . '.<format>'
            ),
            new InputOption(
                'route-prefix',
                'p',
                InputOption::VALUE_REQUIRED,
                'The route prefix'
            ),
            new InputOption(
                'remove',
                'r',
                InputOption::VALUE_NONE,
                'Remove route instead of add.'
            )
        ];
    }

    /**
     * @param InputInterface $input
     *
     * @return RoutingManipulator
     */
    protected function createManipulator(InputInterface $input)
    {
        $manipulator = new RoutingManipulator();
        $manipulator->setRoutingFile($input->getArgument('routing-file'));
        $manipulator->setRoutePrefix($input->getOption('route-prefix'));
        $manipulator->setRemove(($input->getOption('remove') ? true : false));

        return $manipulator;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Routing conf'];
    }
}
