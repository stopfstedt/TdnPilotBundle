<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;

/**
 * Some items borrowed from the GenerateDoctrineCommand and consolidated.
 * Class AbstractGeneratorCommand
 * @package Tdn\PilotBundle\Command
 */
abstract class AbstractGeneratorCommand extends ContainerAwareCommand
{
    /**
     * Override in child class
     * @var string
     */
    const NAME = '';

    /**
     * Override in child class
     * @var string
     */
    const DESCRIPTION = '';

    /**
     * @var string
     */
    private $entity;

    /**
     * @var ManipulatorInterface
     */
    private $manipulator;

    /**
     * @var ClassMetadataInfo
     */
    private $metadata;

    /**
     * @return string
     */
    abstract protected function getFileType();

    /**
     * @param InputInterface          $input
     * @param OutputEngineInterface   $outputEngine
     * @param BundleInterface         $bundle
     * @param ClassMetadataInfo       $metadata
     *
     * @return ManipulatorInterface
     */
    abstract protected function createManipulator(
        InputInterface $input,
        OutputEngineInterface $outputEngine,
        BundleInterface $bundle,
        ClassMetadataInfo $metadata
    );

    /**
     * @param ManipulatorInterface $manipulator
     */
    public function setManipulator(ManipulatorInterface $manipulator)
    {
        $this->manipulator = $manipulator;
    }

    /**
     * @param InputInterface          $input
     * @param OutputEngineInterface   $outputEngine
     * @param BundleInterface         $bundle
     * @param ClassMetadataInfo       $metadata
     *
     * @return ManipulatorInterface
     */
    public function getManipulator(
        InputInterface $input,
        OutputEngineInterface $outputEngine,
        BundleInterface $bundle,
        ClassMetadataInfo $metadata
    ) {
        if (null === $this->manipulator) {
            $this->manipulator = $this->createManipulator(
                $input,
                $outputEngine,
                $bundle,
                $metadata
            );

            $this->manipulator->setOverwrite(($input->getOption('overwrite') ? true : false));
            $this->manipulator->setTargetDirectory($input->getOption('target-directory'));
        }

        return $this->manipulator;
    }

    /**
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $shortcut
     * @return string[]
     */
    public function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)',
                    $entity
                )
            );
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    public function setMetadata(ClassMetadataInfo $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return ClassMetadataInfo
     */
    public function getMetadata()
    {
        if (null === $this->metadata) {
            /** @var ManagerRegistry $doctrine */
            $doctrine = $this->getContainer()->get('doctrine');
            $factory = new DisconnectedMetadataFactory($doctrine);

            $this->setMetadata($factory->getClassMetadata($this->getEntity())->getMetadata()[0]);
        }

        return $this->metadata;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        if (static::NAME == '' || static::DESCRIPTION == '') {
            throw new \RuntimeException(
                'Please set the name and description of the command. Calling class ' . __CLASS__
            );
        }

        $this
            ->setDefinition(array_merge(
                [
                    new InputArgument(
                        'entity',
                        InputArgument::REQUIRED,
                        'The entity class name to initialize (shortcut notation: FooBarBundle:Entity)'
                    ),
                    new InputOption(
                        'overwrite',
                        'o',
                        InputOption::VALUE_NONE,
                        'Overwrite existing ' . $this->getFileType()
                    ),
                    new InputOption(
                        'target-directory',
                        'd',
                        InputOption::VALUE_OPTIONAL,
                        'Specify target directory. Defaults to symfony standard.',
                        null
                    )
                ],
                $this->getInputArgs()
            ))
            ->setDescription(static::DESCRIPTION)
            ->setName(static::NAME);
    }

    /**
     * @param  null|BundleInterface $bundle
     * @return string[]
     */
    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath() . '/Resources/PilotBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir() .
            '/Resources/PilotBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $reflClass = new \ReflectionClass(get_class($this));

        $skeletonDirs[] = dirname($reflClass->getFileName()) . '/../Resources/skeleton';
        $skeletonDirs[] = dirname($reflClass->getFileName()) . '/../Resources';

        return $skeletonDirs;
    }

    /**
     * The magic
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->isInteractive()) {
            if (!$this->getQuestionHelper()->ask(
                $input,
                $output,
                new ConfirmationQuestion(
                    'Do you confirm generation of the ' . $this->getFileType() . ' file(s)?',
                    false
                )
            )
            ) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $entity = Validators::validateEntityName($input->getArgument('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);
        $this->setEntity($this->getContainer()->get('doctrine')->getAliasNamespace($bundle) . '\\' . $entity);
        $bundle                = $this->getContainer()->get('kernel')->getBundle($bundle);
        /** @var OutputEngineInterface $outputEngine */
        $outputEngine          = $this->getContainer()->get('tdn_pilot.output.engine.default');
        $outputEngine->setSkeletonDirs($this->getSkeletonDirs($bundle));

        try {
            $manipulator = $this->getManipulator(
                $input,
                $outputEngine,
                $bundle,
                $this->getMetadata()
            )->prepare();

            if ($manipulator->isValid()) {
                /** @var GeneratedFileInterface $file */
                foreach ($manipulator->generate() as $file) {
                    $output->writeln(sprintf(
                        '<info>The new %s file has been created under %s.</info>',
                        $file->getFilename() . '.' . $file->getExtension(),
                        $file->getFullPath()
                    ));

                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                        $output->writeln(sprintf(
                            '<comment>Contents:</comment>' . PHP_EOL . '%s',
                            $file->getContents()
                        ));
                    }
                }
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return 1;
        }

        return 0;
    }

    /**
     * @return QuestionHelper
     */
    protected function getQuestionHelper()
    {
        return $this->getHelperSet()->get('question');
    }

    /**
     * Override in sub type to add more arguments or options to input.
     * @return array
     */
    protected function getInputArgs()
    {
        return [];
    }
}
