<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Tdn\SfProjectGeneratorBundle\Generator\ControllerGenerator;

/**
 * Class GenerateControllerCommand
 * @package Tdn\SfProjectGeneratorBundle\Command
 */
class GenerateControllerCommand extends GeneratorCommand
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
                new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'The route prefix'),
                new InputOption(
                    'resource',
                    '',
                    InputOption::VALUE_NONE,
                    'The object will return with the resource name'
                ),
                new InputOption(
                    'overwrite',
                    '',
                    InputOption::VALUE_NONE,
                    'Do not stop the generation if rest api controller already exist, thus overwriting all generated files'
                ),
                new InputOption(
                    'document',
                    '',
                    InputOption::VALUE_NONE,
                    'Use NelmioApiDocBundle to document the controller'
                ),
                new InputOption(
                    'with-tests',
                    '',
                    InputOption::VALUE_NONE,
                    'Create functional test class for controller.'
                )
            ))
            ->setDescription('Generates a RESTful controller based on a doctrine entity. Depends on Entity Form Type, Handler, and Manager.')
            ->setHelp(
                <<<EOT
                The <info>tdn:generate:controller</info> command generates a REST api based on a Doctrine entity.

<info>php app/console tdn:generate:rest --entity=AcmeBlogBundle:Post --route-prefix=post_admin</info>

Every generated file is based on a template. There are default templates but they can be overridden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/rest
APP_PATH/Resources/SensioGeneratorBundle/skeleton/rest</info>

And

<info>__bundle_path__/Resources/SensioGeneratorBundle/skeleton/form
__project_root__/app/Resources/SensioGeneratorBundle/skeleton/form</info>

You can check https://github.com/sensio/SensioGeneratorBundle/tree/master/Resources/skeleton
in order to know the file structure of the skeleton
EOT
            )
            ->setName('tdn:generate:controller');
    }

    public function setOptions(InputInterface $input)
    {
        $this->options = [
            'route-prefix' => $this->getRoutePrefix($input),
            'overwrite'    => $input->getOption('overwrite'),
            'resource'     => $input->getOption('resource'),
            'document'     => $input->getOption('document'),
            'with-tests'   => ($input->hasOption('with-tests') ?$input->getOption('with-tests') : null) // generateTestClass()
        ];
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getRoutePrefix(InputInterface $input)
    {
        $prefix = $input->getOption('route-prefix') ?: strtolower(str_replace(array('\\', '/'), '_', $this->entity));

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
    }

    /**
     * @return ControllerGenerator
     */
    protected function createGenerator()
    {
        return new ControllerGenerator();
    }

    /**
     * @return string
     */
    protected function getFileTypeCreated()
    {
        return 'Controller Class';
    }
}
