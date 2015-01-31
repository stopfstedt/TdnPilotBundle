<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

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
                    'rest-support',
                    '',
                    InputOption::VALUE_OPTIONAL,
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

        return parent::configure();
    }

    /**
     * @return FormGenerator
     */
    protected function createGenerator()
    {
        return new FormGenerator();
    }

    /**
     * Should set options array.
     * @param InputInterface $input
     */
    public function setOptions(InputInterface $input)
    {
        $this->options = [
            'rest-support' => ($input->hasOption('rest-support') ? true : false)
        ];
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Form Class';
    }
}
