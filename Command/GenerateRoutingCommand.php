<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Tdn\SfProjectGeneratorBundle\Generator\RoutingGenerator;

/**
 * Class GenerateRoutingCommand
 * @package Tdn\SfRoutingGeneratorBundle\Command
 */
class GenerateRoutingCommand extends GeneratorCommand
{
    const DEFAULT_ROUTING = "Resources/config/routing.yml"; //@todo: Support multiple formats

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument(
                    'entity',
                    InputArgument::REQUIRED,
                    'The entity class name to initialize (shortcut notation)'
                ),
                new InputArgument(
                    'routing-file',
                    InputArgument::OPTIONAL,
                    'The routing file, defaults to: ' . self::DEFAULT_ROUTING,
                    self::DEFAULT_ROUTING
                ),
                new InputOption(
                    'route-prefix',
                    '',
                    InputOption::VALUE_REQUIRED,
                    'The route prefix'
                ),
                new InputOption(
                    'remove',
                    null,
                    InputOption::VALUE_NONE
                ),
                new InputOption(
                    'overwrite',
                    'w',
                    InputOption::VALUE_NONE,
                    'Overwrite existing form type.'
                )
            ))
            ->setDescription('Generates the routing configuration for a RESTFul controller based on an entity. Removes it with the --remove flag.')
            ->setHelp(<<<EOT
The <info>tdn:generate:routing</info> command generates a routing file for a RESTFul controller based on a doctrine entity.

<info>php app/console tdn:generate:routing AcmeBlogBundle:Post</info>
<info>php app/console tdn:generate:routing AcmeBlogBundle:Post --remove</info>

Every generated file is based on a template. There are default templates but they can be overriden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/entity
APP_PATH/Resources/SensioGeneratorBundle/skeleton/entity</info>
EOT
            )
            ->setName('tdn:generate:routing')
        ;
    }

    /**
     * @return RoutingGenerator
     */
    protected function createGenerator()
    {
        return new RoutingGenerator();
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Routing Configuration';
    }

    protected function setOptions(InputInterface $input)
    {
        $this->options = new ArrayCollection([
            'routing-file' => $input->getArgument('routing-file'),
            'remove' => ($input->getOption('remove') ? true : false),
            'prefix' => $input->getOption('route-prefix'),
            'overwrite' => ($input->getOption('overwrite') ? true : false)
        ]);
    }
}
