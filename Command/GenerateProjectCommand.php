<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Tdn\SfProjectGeneratorBundle\Generator\ProjectGenerator;

/**
 * Class GenerateProjectCommand
 * @package Tdn\SfProjectGeneratorBundle\Command
 */
class GenerateProjectCommand extends GeneratorCommand
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
                )
            ))
            ->setDescription('Generates a majority of boilerplate code from an entity file. See help for more details.')
            ->setHelp(<<<EOT
The <info>tdn:generate:project</info> command generates a REST project based on a Doctrine entity. Requires manager to be present.

<info>php app/console tdn:generate:project AcmeBlogBundle:Post</info>

Every generated file is based on a template. There are default templates but they can be overridden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/entity
APP_PATH/Resources/SensioGeneratorBundle/skeleton/entity</info>
EOT
            )
            ->setName('tdn:generate:project')
        ;
    }

    /**
     * @return EntityGenerator
     */
    public function createGenerator()
    {
        return new ProjectGenerator();
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Project';
    }

    /**
     * @param InputInterface $input
     */
    public function setOptions(InputInterface $input)
    {
        $this->options = [];
    }

}
