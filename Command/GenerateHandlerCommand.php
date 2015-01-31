<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Tdn\SfProjectGeneratorBundle\Generator\HandlerGenerator;

/**
 * Class GenerateHandlerCommand
 * @package Tdn\SfProjectGeneratorBundle\Command
 */
class GenerateHandlerCommand extends GeneratorCommand
{
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
                new InputOption(
                    'overwrite',
                    'w',
                    InputOption::VALUE_OPTIONAL,
                    false
                )
            ))
            ->setDescription(
                'Generates an entity REST handler file for a controller. Depends on Entity Manager. Depended ON by controller.'
            )
            ->setHelp(
<<<EOT
The <info>tdn:generate:handler</info> command generates a REST handler based on a Doctrine entity. Requires manager to be present.

<info>php app/console tdn:generate:handler AcmeBlogBundle:Post</info>

Every generated file is based on a template. There are default templates but they can be overridden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/entity
APP_PATH/Resources/SensioGeneratorBundle/skeleton/entity</info>
EOT
            )
            ->setName('tdn:generate:handler')
        ;
    }

    /**
     * @return EntityGenerator
     */
    public function createGenerator()
    {
        return new HandlerGenerator();
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Handler Class';
    }

    /**
     * @param InputInterface $input
     */
    public function setOptions(InputInterface $input)
    {
        $this->options = [
            'overwrite' => ($input->getOption('overwrite') !== false) ? true : false
        ];
    }
}
