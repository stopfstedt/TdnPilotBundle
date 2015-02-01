<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Doctrine\Common\Collections\ArrayCollection;

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
                    'entity-dir',
                    InputArgument::REQUIRED,
                    'The entity dir to generate a project from.'
                ),
                new InputOption(
                    'overwrite',
                    'w',
                    InputOption::VALUE_NONE,
                    'Overwrite already existing files'
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
     * The magic
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->isInteractive()) {
            if (!$this->getQuestionHelper()->ask(
                $input,
                $output,
                new ConfirmationQuestion(
                    sprintf(
                        'Do you confirm generation of the %s based on entities located in %s? %s',
                        $this->getFileTypeCreated(),
                        $input->getArgument('entity-dir'),
                        ($input->hasOption('overwrite')) ? '(overwrite option is set, all previously generated/modified files will be overwritten)' : ''
                    ),
                    false
                )
            )
            ) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $entityFiles = $this->getEntityFiles($input->getArgument('entity-dir'));

        if ($entityFiles->isEmpty()) {
            $output->writeln('No entity files found.');
            return 0;
        }

        //@TODO:
        //Get Controller options
        // is resource? document? with tests?
        //Get FormType options
        // rest support?
        //Get Handler options
        //Get Manager Options
        //Get Routing options
        // route prefix? routing file

        /** @var SplFileInfo $entityFie */
        foreach ($entityFiles as $entityFie) {
            try {
                $bundle = $this->getBundleNameFromPath($entityFie->getRealPath());
                $entity = $entityFie->getBasename();
                $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
                $metadata = $this->getEntityMetadata($entityClass);
                $bundle   = $this->getApplication()->getKernel()->getBundle($bundle);
                $generator = $this->getGenerator($bundle);
                $generator->setFilesystem($this->getContainer()->get('filesystem'));
                $generator->generate($bundle, $entity, $metadata[0], $options);
                $output->writeln(sprintf(
                    'The following files have been created for the %s entity: ' . PHP_EOL . '%s%s%s%s%s%s',
                    'Interface' . PHP_EOL,
                    'FormType' . PHP_EOL,
                    'Handler' . PHP_EOL,
                    'Manager' . PHP_EOL,
                    'Routing' . PHP_EOL,
                    'Controller' . PHP_EOL
                ));
            } catch (\Exception $e) {
                $output->writeln($this->getFailureMessage($e));
            }
        }
    }

    /**
     * @param $path
     * @return string
     */
    protected function getBundleNameFromPath($path)
    {
        return '';
    }

    /**
     * Returns all entity files in a directory
     * @param string $dir
     *
     * @return ArrayCollection
     * @throws \Exception
     */
    protected function getEntityFiles($dir)
    {
        $entityFiles = new ArrayCollection();
        if (is_dir($dir)) {
            $finder = new Finder();
            $finder
                ->files()
                ->in($dir)
                ->name('*.php')
                ->notName('*Interface*')
                ->depth(0);
            /** @var SplFileInfo $fileIndex */
            foreach ($finder as $fileIndex) {
                $entityFiles->add($fileIndex->openFile());
            }
        } else {
            throw new \InvalidArgumentException("Invalid directory.");
        }

        return $entityFiles;
    }

    /**
     * @return ProjectGenerator
     */
    protected function createGenerator()
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
     * @throws \Exception
     */
    protected function setOptions(InputInterface $input)
    {
        throw new \RuntimeException('Not implemented in this class.');
    }

}
