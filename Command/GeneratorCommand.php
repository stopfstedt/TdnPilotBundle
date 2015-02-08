<?php

namespace Tdn\SfProjectGeneratorBundle\Command;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand as BaseGeneratorCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Tdn\SfProjectGeneratorBundle\Generator\GeneratorInterface;

/**
 * Here to modify skeleton dirs....need to figure out better way around that.
 * Some items borrowed from the GenerateDoctrineCommand and consolidated.
 *
 * Class GeneratorCommand
 * @package Tdn\SfProjectGeneratorBundle\Command
 */
abstract class GeneratorCommand extends BaseGeneratorCommand
{
    /**
     * @var ArrayCollection
     */
    protected $options;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @return string
     */
    abstract protected function getFileTypeCreated();

    /**
     * Should set options array.
     * Override in child class to set any options.
     *
     * @param InputInterface $input
     */
    abstract protected function setOptions(InputInterface $input);

    /**
     * @return Collection
     */
    protected function getOptions()
    {
        if (null === $this->options || !$this->options instanceof Collection) {
            throw new \BadFunctionCallException('Options must be set by child class as an instance of Collection.');
        }

        return $this->options;
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    protected function getFailureMessage(\Exception $exception)
    {
        return 'Failed to create ' . $this->getFileTypeCreated() . ' file. Error: ' . $exception->getMessage();
    }

    /**
     * @param BundleInterface $bundle
     * @return GeneratorInterface
     */
    protected function getGenerator(BundleInterface $bundle = null)
    {
        return parent::getGenerator($bundle);
    }

    /**
     * @param BundleInterface $bundle
     * @return array
     */
    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $reflClass = new \ReflectionClass(get_class($this));

        $skeletonDirs[] = dirname($reflClass->getFileName()).'/../Resources/skeleton';
        $skeletonDirs[] = dirname($reflClass->getFileName()).'/../Resources';

        return $skeletonDirs;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return class_exists('Doctrine\\Bundle\\DoctrineBundle\\DoctrineBundle');
    }

    /**
     * @param $shortcut
     * @return array
     */
    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    /**
     * @param $entity
     * @return array
     */
    protected function getEntityMetadata($entity)
    {
        $factory = new DisconnectedMetadataFactory($this->getContainer()->get('doctrine'));

        return $factory->getClassMetadata($entity)->getMetadata();
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
                new ConfirmationQuestion('Do you confirm generation of the ' . $this->getFileTypeCreated() . '? ', false)
            )
            ) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $this->entity = Validators::validateEntityName($input->getArgument('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($this->entity);

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata = $this->getEntityMetadata($entityClass);
        $bundle   = $this->getApplication()->getKernel()->getBundle($bundle);
        $generator = $this->getGenerator();
        $generator->setFilesystem($this->getContainer()->get('filesystem'));

        try {
            $this->setOptions($input);
            $generator->generate($bundle, $entity, $metadata[0], $this->getOptions());

            $output->writeln(sprintf(
                '<info>The new %s %s file has been created under %s.</info>',
                $generator->getGeneratedName(),
                $this->getFileTypeCreated(),
                $generator->getFilePath()
            ));
        } catch (\Exception $e) {
            $output->writeln("<error>" . $this->getFailureMessage($e) . "</error>");
        }
    }
}
