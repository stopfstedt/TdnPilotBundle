<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Tdn\SfProjectGeneratorBundle\Generator\EntityGenerator;

/**
 * Class GenerateEntityInterfaceCommand
 * @package Tdn\SfProjectGeneratorBundle\Command
 */
class GenerateEntityInterfaceCommand extends GeneratorCommand
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
                    'The entity class name to create an interface for. (shortcut notation)'
                )
            ))
            ->setDescription('Generates an entity interface from an entity file.')
            ->setHelp(<<<EOT
The <info>tdn:generate:entity:interface</info> command generates an interface based on a Doctrine entity.

<info>php app/console tdn:generate:entity:interface AcmeBlogBundle:Post</info>

Every generated file is based on a template. There are default templates but they can be overridden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/entity
APP_PATH/Resources/SensioGeneratorBundle/skeleton/entity</info>
EOT
            )
            ->setName('tdn:generate:entity:interface')
        ;
    }

    /**
     * @return EntityGenerator
     */
    public function createGenerator()
    {
        return new EntityGenerator();
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Entity Interface';
    }

    /**
     * @param InputInterface $input
     */
    public function setOptions(InputInterface $input)
    {
        $this->options = [];
    }
}
