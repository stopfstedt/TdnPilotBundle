<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Tdn\SfProjectGeneratorBundle\Generator\ManagerGenerator;

/**
 * Class GenerateManagerCommand
 * @package Tdn\SfProjectGeneratorBundle\Command
 */
class GenerateManagerCommand extends GeneratorCommand
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
                    InputOption::VALUE_NONE,
                    'Overwrite existing manager'
                )
            ))
            ->setDescription('Generates an manager from an entity file.')
            ->setHelp(<<<EOT
The <info>tdn:generate:manager</info> command generates a  an manager based on a Doctrine entity.

<info>php app/console tdn:generate:manager AcmeBlogBundle:Post</info>

Every generated file is based on a template. There are default templates but they can be overriden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/entity
APP_PATH/Resources/SensioGeneratorBundle/skeleton/entity</info>
EOT
            )
            ->setName('tdn:generate:manager')
        ;
    }

    /**
     * @return ManagerGenerator
     */
    protected function createGenerator()
    {
        return new ManagerGenerator();
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Manager Class';
    }

    /**
     * @param InputInterface $input
     */
    protected function setOptions(InputInterface $input)
    {
        $this->options = new ArrayCollection([
            'overwrite' => ($input->getOption('overwrite') ? true : false)
        ]);
    }
}
