<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Tdn\PilotBundle\Manipulator\ManipulatorInterface;
use Tdn\PilotBundle\Manipulator\ServiceManipulatorInterface;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\Services\Doctrine\EntityUtils;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyUtils;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Class AbstractGeneratorCommand
 *
 * Basic class that all generator commands can extend.
 *
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
     * @var EntityUtils
     */
    private $entityUtils;

    /**
     * @var ManipulatorInterface
     */
    private $manipulator;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @return string[]
     */
    abstract protected function getFiles();

    /**
     * @return ManipulatorInterface
     */
    abstract protected function createManipulator();

    /**
     * @param ManipulatorInterface $manipulator
     */
    public function setManipulator(ManipulatorInterface $manipulator)
    {
        $this->manipulator = $manipulator;
    }

    /**
     * Returns current manipulator if set, otherwise returns a new instance.
     *
     * Originally this function internally assigned the variable to cache it, but ran into
     * issues with support entities.
     *
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     *
     * @return ManipulatorInterface
     */
    public function getManipulator(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        if ($this->manipulator) {
            return $this->manipulator;
        } else {
            $manipulator = $this->createManipulator();

            $manipulator->setMetadata($metadata);
            $manipulator->setTemplateStrategy($templateStrategy);
            $manipulator->setBundle($bundle);
            $manipulator->setOverwrite(($this->getInput()->getOption('overwrite') ? true : false));
            $manipulator->setTargetDirectory($this->getInput()->getOption('target-directory'));

            if ($manipulator instanceof ServiceManipulatorInterface) {
                $manipulator->setServiceUtils($this->getContainer()->get('tdn_pilot.di.service.utils'));
                //Maybe add an abstract service generator command that adds this option
                $manipulator->setFormat($this->getInput()->getOption('format'));
            }

            return $manipulator;
        }
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
     * @param EntityUtils $entityUtils
     */
    public function setEntityUtils(EntityUtils $entityUtils)
    {
        $this->entityUtils = $entityUtils;
    }

    /**
     * @return EntityUtils
     */
    public function getEntityUtils()
    {
        if (null === $this->entityUtils) {
            $this->setEntityUtils(new EntityUtils());
        }

        return $this->entityUtils;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        if (static::NAME == '' || static::DESCRIPTION == '') {
            throw new \RuntimeException(
                'Please set the name and description of the command. Error in: ' . get_called_class()
            );
        }

        $this
            ->setDefinition(array_merge(
                [
                    new InputOption(
                        'entity',
                        't',
                        InputOption::VALUE_OPTIONAL,
                        'The entity class name to initialize (shortcut notation: FooBarBundle:Entity)',
                        null
                    ),
                    new InputOption(
                        'entities-location',
                        'l',
                        InputOption::VALUE_OPTIONAL,
                        'The directory containing the entities classes to target',
                        null
                    ),
                    new InputOption(
                        'overwrite',
                        'o',
                        InputOption::VALUE_NONE,
                        'Overwrite existing ' . implode(', ', $this->getFiles())
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
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        $this->input = $input;
    }

    /**
     * Generates the files based on the options provided.
     *
     * Sets up all dependencies for manipulator, prepares it
     * and ultimately tells it to generate it's files.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureValidInput($input, $output);

        $entities = $this->getEntityUtils()->getEntityDirAsCollection($input->getOption('entities-location'));
        if (null !== $entity = $input->getOption('entity')) {
            $entities->add($entity);
        }

        $doctrine = $this->getManagerRegistry();
        $templateStrategy = $this->getTemplateStrategy();

        foreach ($entities as $entity) {
            $entity = Validators::validateEntityName($entity);
            list($bundle, $entity) = $this->getEntityUtils()->parseShortcutNotation($entity);
            $this->setEntity($doctrine->getAliasNamespace($bundle) . '\\' . $entity);
            $bundle = $this->getKernel()->getBundle($bundle);
            $templateStrategy->setSkeletonDirs(
                $this->getTemplateStrategyUtils()->getDefaultSkeletonDirs($bundle)
            );

            try {
                $manipulator = $this->getManipulator(
                    $templateStrategy,
                    $bundle,
                    $this->getEntityUtils()->getMetadata($doctrine, $this->getEntity())
                )->prepare();

                if ($this->shouldContinue($input, $output, $manipulator->getGeneratedFiles(), $entity)) {
                    $output->write(PHP_EOL);
                    foreach ($manipulator->generate() as $file) {
                        $this->printFileGeneratedMessage($output, $file);
                    }
                    $output->write(PHP_EOL);
                    foreach ($manipulator->getMessages() as $message) {
                        $output->writeln(
                            sprintf(
                                '<comment>%s</comment>',
                                $message
                            )
                        );
                    }
                    $output->write(PHP_EOL);
                } else {
                    $output->writeln('<error>Generation cancelled.</error>');

                    return 1;
                }
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return 1;
            }
        }

        return 0;
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->getContainer()->get('kernel');
    }

    /**
     * @return ManagerRegistry
     */
    protected function getManagerRegistry()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return TemplateStrategyInterface
     */
    protected function getTemplateStrategy()
    {
        return $this->getContainer()->get('tdn_pilot.template.strategy.default');
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

    /**
     * @param OutputInterface $output
     * @param GeneratedFileInterface $file
     */
    protected function printFileGeneratedMessage(OutputInterface $output, GeneratedFileInterface $file)
    {
        $output->writeln(sprintf(
            'The new <info>%s</info> file has been created under <info>%s</info>.',
            $file->getFilename() . '.' . $file->getExtension(),
            $file->getFullPath()
        ));

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf(
                'Contents:' . PHP_EOL . '%s',
                $file->getContents()
            ));
        }
    }

    /**
     * Confirms generation
     *
     * If input is interactive asks to confirm generation of files. You have to explicitly
     * confirm to continue.
     * If input is **not** interactive, then it will automatically return true.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param ArrayCollection $generatedFiles
     * @param string          $entity
     *
     * @return bool
     */
    protected function shouldContinue(
        InputInterface $input,
        OutputInterface $output,
        ArrayCollection $generatedFiles,
        $entity
    ) {
        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion(
                sprintf(
                    'Entity: %s - File(s):' .
                    PHP_EOL . '<info>%s</info>' .
                    PHP_EOL . 'Do you confirm generation/manipulation of the files listed above (y/n)?',
                    $entity,
                    implode(PHP_EOL, $generatedFiles->map(function (GeneratedFileInterface $generatedFile) {
                        return '- ' . $generatedFile->getFullPath();
                    })->toArray())
                ),
                false
            );

            return (bool) $this->getQuestionHelper()->ask($input, $output, $question);
        }

        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \RuntimeException when both entity and entities-location are set or both are not set.
     */
    protected function ensureValidInput(InputInterface $input, OutputInterface $output)
    {
        if (($input->getOption('entity') === null && $input->getOption('entities-location') === null) ||
            ($input->getOption('entity') !== null && $input->getOption('entities-location') !== null)
        ) {
            $output->writeln('<error>Please use either entity OR entities-location. One is required.</error>');

            throw new \RuntimeException();
        }
    }

    /**
     * @return InputInterface
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * @return TemplateStrategyUtils
     */
    protected function getTemplateStrategyUtils()
    {
        return new TemplateStrategyUtils();
    }
}
