<?php

namespace Tdn\PilotBundle\Manipulator;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Abstract Class AbstractManipulator
 *
 * Parent manipulator class. Each manipulator deals with a specific part
 * of a target application (controllers, managers, handlers, routing, etc)
 * When adding a new type of manipulation, a new manipulator should be
 * created extending this class.
 *
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractManipulator implements ManipulatorInterface
{
    /**
     * @var TemplateStrategyInterface
     */
    private $templateStrategy;

    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var ClassMetadata
     */
    private $metadata;

    /**
     * @var ArrayCollection|GeneratedFileInterface[]
     */
    private $generatedFiles;

    /**
     * @var ArrayCollection|SplFileInfo[]
     */
    private $fileDependencies;

    /**
     * @var ArrayCollection|string[]
     */
    private $messages;

    /**
     * @var boolean
     */
    private $overwrite;

    /**
     * @var string
     */
    private $targetDirectory;

    public function __construct() {
        $this->generatedFiles   = new ArrayCollection();
        $this->fileDependencies = new ArrayCollection();
        $this->messages         = new ArrayCollection();
        $this->setOverwrite(false);
    }

    public function setTemplateStrategy(TemplateStrategyInterface $templateStrategy)
    {
        $this->templateStrategy = $templateStrategy;
    }

    /**
     * @return TemplateStrategyInterface
     */
    public function getTemplateStrategy()
    {
        return $this->templateStrategy;
    }

    /**
     * @param BundleInterface $bundle
     */
    public function setBundle(BundleInterface $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @return BundleInterface
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->cleanMetadataProperty($this->getMetadata()->getName());
    }

    /**
     * @return string
     */
    public function getEntityNamespace()
    {
        return $this->cleanMetadataProperty($this->getMetadata()->namespace);
    }

    /**
     * @param ClassMetadata $metadata
     */
    public function setMetadata(ClassMetadata $metadata)
    {
        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException(sprintf(
                'The %s does not support entity classes with multiple primary keys.',
                __CLASS__
            ));
        }

        $this->metadata = $metadata;
    }

    /**
     * @return ClassMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Collection $generatedFiles
     */
    public function setGeneratedFiles(Collection $generatedFiles)
    {
        $this->generatedFiles = new ArrayCollection();

        foreach ($generatedFiles as $generatedFile) {
            $this->addGeneratedFile($generatedFile);
        }
    }

    /**
     * @param GeneratedFileInterface $generatedFile
     */
    public function addGeneratedFile(GeneratedFileInterface $generatedFile)
    {
        $this->generatedFiles->set($generatedFile->getFullPath(), $generatedFile);
    }

    /**
     * @return ArrayCollection|GeneratedFileInterface[]
     */
    public function getGeneratedFiles()
    {
        return $this->generatedFiles;
    }

    /**
     * @param Collection $fileDependencies
     */
    public function setFileDependencies(Collection $fileDependencies)
    {
        $this->fileDependencies = new ArrayCollection();

        foreach ($fileDependencies as $fileDependency) {
            $this->addFileDependency($fileDependency);
        }
    }

    /**
     * @param SplFileInfo $fileDependency
     */
    public function addFileDependency(SplFileInfo $fileDependency)
    {
        $this->fileDependencies->add($fileDependency);
    }

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    public function getFileDependencies()
    {
        return $this->fileDependencies;
    }

    /**
     * @param Collection $messages
     */
    public function setMessages(Collection $messages)
    {
        $this->messages = new ArrayCollection();

        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * @param string $message
     */
    public function addMessage($message)
    {
        $this->messages->add($message);
    }

    /**
     * @return ArrayCollection|string[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param string $targetDirectory
     */
    public function setTargetDirectory($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @return string
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * @param bool $overwrite
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

    /**
     * @return bool
     */
    public function shouldOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * Ensures the state of the bundle is valid for our generation purposes.
     *
     * Iterates through file dependencies and generated files to ensure
     * rules set against them pass. This should be always called if extending
     * this method with parent::isValid()
     *
     * @throws \RunTimeException
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->getFileDependencies() as $fileDependency) {
            if ($this->isDependencyValid($fileDependency)) {
                continue;
            }
        }

        foreach ($this->getGeneratedFiles() as $generatedFile) {
            if ($this->isGeneratedFileValid($generatedFile)) {
                continue;
            }
        }

        return true;
    }

    /**
     * Generates all the files declared by the manipulator if the
     * system is in a valid state.
     *
     * @return ArrayCollection|GeneratedFileInterface[]
     */
    public function generate()
    {
        if ($this->isValid()) {
            foreach ($this->getGeneratedFiles() as $generatedFile) {
                if ((!$generatedFile->isAuxFile() || !$generatedFile->isServiceFile()) && $this->shouldOverwrite()
                    && file_exists($generatedFile->getFullPath())
                ) {
                    unlink($generatedFile->getFullPath());
                }

                $this->getTemplateStrategy()->renderFile($generatedFile);
            }

            return $this->getGeneratedFiles();
        }

        return new ArrayCollection();
    }

    /**
     * Ensures that dependency files exist
     *
     * Certain objects we're generating declare their dependencies
     * on other objects. This ensures those dependencies exist.
     *
     * @param SplFileInfo $fileDependency
     * @return bool
     */
    protected function isDependencyValid(SplFileInfo $fileDependency)
    {
        if (!$fileDependency->isFile() || !$fileDependency->isReadable()) {
            throw new \RuntimeException(sprintf(
                'Please ensure the file %s exists and is readable.',
                $fileDependency->getRealPath()
            ));
        }

        return true;
    }

    /**
     * Ensures that files can be written without conflict
     *
     * Of if a conflict is present, that the class has been configured
     * to properly handle that conflict.
     *
     * @param GeneratedFileInterface $generatedFile
     * @return bool
     */
    protected function isGeneratedFileValid(GeneratedFileInterface $generatedFile)
    {
        if (file_exists($generatedFile->getFullPath()) &&
            (!$this->shouldOverwrite() && !$generatedFile->isAuxFile() && !$generatedFile->isServiceFile())
        ) {
            throw new \RuntimeException(sprintf(
                'Unable to generate the %s form class as it already exists under the file: %s',
                $generatedFile->getFilename(),
                $generatedFile->getFullPath()
            ));
        }

        return true;
    }

    /**
     * Gets the entity's fields.
     *
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param  ClassMetadata $metadata
     *
     * @return array $fields
     */
    protected function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        $fields = array_merge($metadata->fieldMappings, $metadata->associationMappings);

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            foreach ($metadata->identifier as $identifier) {
                unset($fields[$identifier]);
            }
        }

        $multiTypes = array(
            ClassMetadata::ONE_TO_MANY,
            ClassMetadata::MANY_TO_MANY,
        );

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if (in_array($relation['type'], $multiTypes)) {
                $fields[$fieldName]['relatedType'] = 'many';
            } else {
                $fields[$fieldName]['relatedType'] = 'single';
            }

            $fields[$fieldName]['relatedEntityShortcut'] =
                $this->getEntityBundleShortcut($fields[$fieldName]['targetEntity']);
        }

        return $fields;
    }

    /**
     * Gets the short version of a Entity's FQDN
     *
     * Take an entity name and return the shortcut name
     * eg Acme\DemoBundle\Entity\Notes -> AcemDemoBundle:Notes
     *
     * @param string $entity Fully qualified class name of the entity
     *
     * @return string
     */
    protected function getEntityBundleShortcut($entity)
    {
        // wrap in EntityManager's Class Metadata function avoid problems with cached proxy classes
        $path = explode('\Entity\\', $entity);
        return str_replace('\\', '', $path[0]) . ':' . $path[1];
    }

    /**
     * Cleans properties with namespaces appended to them.
     *
     * This method always assumes that the directory for entities
     * will be `Entity` (the doctrine standard).
     *
     * @param string $property
     *
     * @return string
     */
    private function cleanMetadataProperty($property)
    {
        $parts = explode('\\', $property);
        $realProperty = String::create(array_pop($parts));

        return ((string) $realProperty->toLowerCase() !== 'entity') ? (string) $realProperty : '';
    }
}
