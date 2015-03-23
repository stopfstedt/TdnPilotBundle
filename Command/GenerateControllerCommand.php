<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

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
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     *
     * @return ControllerManipulator
     */
    protected function createManipulator(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        $manipulator = new ControllerManipulator($templateStrategy, $bundle, $metadata);
        $manipulator->setResource(($this->getInput()->getOption('resource') ? true : false));
        $manipulator->setSwagger(($this->getInput()->getOption('with-swagger') ? true : false));
        $manipulator->setRoutePrefix($this->getRoutePrefix($this->getInput()->getOption('route-prefix')));
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
