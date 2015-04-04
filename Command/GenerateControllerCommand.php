<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
     * @var bool
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $swagger;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addOption(
                'resource',
                'r',
                InputOption::VALUE_NONE,
                'The object will return with the resource name'
            )
            ->addOption(
                'with-swagger',
                'g',
                InputOption::VALUE_NONE,
                'Use NelmioApiDocBundle (which uses swagger-ui) to document the controller'
            )
            ->addOption(
                'route-prefix',
                'p',
                InputOption::VALUE_NONE,
                'If using annotations, you should also add a route prefix to the controller.'
            )
        ;

        parent::configure();
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
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->resource = ($input->getOption('resource') ? true : false);
        $this->swagger  = ($input->getOption('with-swagger') ? true : false);
        $this->routePrefix = $this->getRoutePrefix($input->getOption('route-prefix'));
    }

    /**
     * @return ControllerManipulator
     */
    protected function createManipulator()
    {
        $manipulator = new ControllerManipulator();
        $manipulator->setResource($this->resource);
        $manipulator->setSwagger($this->swagger);
        $manipulator->setRoutePrefix($this->routePrefix);
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
