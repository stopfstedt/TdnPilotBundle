<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Tdn\SfProjectGeneratorBundle\Generator\FormGenerator;

/**
 * Generates a form type class for a given Doctrine entity, with optional REST generator support.
 *
 * @author Victor Passapera <vpassapera@gmail.com>
 */
class GenerateFormCommand extends GeneratorCommand
{
    private $formGenerator;

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
                    'Overwrite existing form type.'
                ),
                new InputOption(
                    'rest-support',
                    '',
                    InputOption::VALUE_NONE,
                    'Generate an form type with tdn_entity support'
                )
            ))
            ->setDescription('Generates a form type class based on a doctrine entity.')
            ->setHelp(<<<EOT
The <info>doctrine:generate:form</info> command generates a form class based on a Doctrine entity.

<info>php app/console doctrine:generate:form AcmeBlogBundle:Post</info>

Every generated file is based on a template. There are default templates but they can be overriden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/form
APP_PATH/Resources/SensioGeneratorBundle/skeleton/form</info>

You can check https://github.com/sensio/SensioGeneratorBundle/tree/master/Resources/skeleton
in order to know the file structure of the skeleton
EOT
            )
            ->setName('tdn:generate:form')
        ;
    }

    /**
     * @return FormGenerator
     */
    protected function createGenerator()
    {
        return new FormGenerator();
    }

    /**
     * @param InputInterface $input
     */
    protected function setOptions(InputInterface $input)
    {
        $this->options = new ArrayCollection([
            'rest-support' => ($input->getOption('rest-support') ? true : false),
            'overwrite' => ($input->getOption('overwrite') ? true : false)
        ]);
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Form Class';
    }
}
