<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;

/**
 * Class GenerateControllerCommand
 *
 * Generates a CRUD controller based on an entity.
 *
 * @package Tdn\PilotBundle\Command
 */
class GenerateControllerCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'tdn:generate:controller';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates a Restful controller based on a doctrine entity.';

    /**
     * @return InputOption[]
     */
    protected function getInputArgs()
    {
        return [
            new InputOption(
                'resource',
                'r',
                InputOption::VALUE_NONE,
                'The object will return with the resource name'
            ),
            new InputOption(
                'with-swagger',
                'g',
                InputOption::VALUE_NONE,
                'Use NelmioApiDocBundle (which uses swagger-ui) to document the controller'
            ),
            new InputOption(
                'route-prefix',
                'p',
                InputOption::VALUE_NONE,
                'If using annotations, you should also add a route prefix to the controller.'
            )
        ];
    }

    /**
     * Gets the route prefix for the resource
     *
     * Gets a route prefix to use when using annotations. Otherwise the route prefix
     * is set through the `RoutingManipulator`.
     *
     * @param  string $routePrefix
     *
     * @return string
     */
    public function getRoutePrefix($routePrefix = '')
    {
        $prefix = !empty($routePrefix) ? $routePrefix:
            strtolower(str_replace(array('\\', '/'), '_', $this->getEntity()));

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
    }

    /**
     * @param InputInterface $input
     *
     * @return ControllerManipulator
     */
    protected function createManipulator(InputInterface $input)
    {
        $manipulator = new ControllerManipulator();
        $manipulator->setResource(($input->getOption('resource') ? true : false));
        $manipulator->setSwagger(($input->getOption('with-swagger') ? true : false));
        $manipulator->setRoutePrefix($this->getRoutePrefix($input->getOption('route-prefix')));
        $manipulator->setGenerateTests(false);

        return $manipulator;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Rest Controller'];
    }
}
