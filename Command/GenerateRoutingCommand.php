<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Manipulator\RoutingManipulator;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

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
    const DEFAULT_ROUTING = 'routing.yml';

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
                self::DEFAULT_ROUTING
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
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     *
     * @return RoutingManipulator
     */
    protected function createManipulator(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        $manipulator = new RoutingManipulator($templateStrategy, $bundle, $metadata);
        $manipulator->setRoutingFile($this->getInput()->getArgument('routing-file'));
        $manipulator->setRoutePrefix($this->getInput()->getOption('route-prefix'));
        $manipulator->setRemove(($this->getInput()->getOption('remove') ? true : false));

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
