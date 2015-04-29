<?php

namespace Tdn\PilotBundle\Manipulator;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Model\Format;
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
     * @var ArrayCollection|File[]
     */
    private $files;

    /**
     * @var ArrayCollection|File[]
     */
    private $fileDependencies;

    /**
     * @var ArrayCollection|string[]
     */
    private $messages;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var boolean
     */
    private $overwrite;

    /**
     * @var string
     */
    private $format;

    public function __construct()
    {
        $this->files            = new ArrayCollection();
        $this->fileDependencies = new ArrayCollection();
        $this->messages         = new ArrayCollection();
        $this->setOverwrite(false);
        $this->setFormat(Format::YAML);
    }

    /**
     * @return static
     */
    public function reset()
    {
        return new static();
    }

    /**
     * @return array
     */
    public static function getSupportedFormats()
    {
        return [
            Format::YAML,
            Format::YML,
            Format::XML,
            Format::ANNOTATION
        ];
    }

    /**
     * @param TemplateStrategyInterface $templateStrategy
     */
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
     * @param Collection $files
     */
    public function setFiles(Collection $files)
    {
        $this->files = new ArrayCollection();

        foreach ($files as $generatedFile) {
            $this->addFile($generatedFile);
        }
    }

    /**
     * @param File $file
     */
    public function addFile(File $file)
    {
        $this->files->set($file->getRealPath(), $file);
    }

    /**
     * @return ArrayCollection|File[]
     */
    public function getFiles()
    {
        return $this->files;
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
     * @param File $fileDependency
     */
    public function addFileDependency(File $fileDependency)
    {
        $this->fileDependencies->add($fileDependency);
    }

    /**
     * @return ArrayCollection|File[]
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
        $this->messages->add((string) $message);
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
        return realpath($this->targetDirectory);
    }

    /**
     * @param bool $overwrite
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

    /**
     * True if the generated files should be overwritten.
     *
     * @return bool
     */
    public function shouldOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        if (!in_array($format, self::getSupportedFormats())) {
            throw new \InvalidArgumentException('Invalid format ' . $format);
        }

        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
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

        foreach ($this->getFiles() as $generatedFile) {
            if ($this->isFileValid($generatedFile)) {
                continue;
            }
        }

        return true;
    }

    /**
     * Generates all the files declared by the manipulator if the
     * system is in a valid state.
     *
     * @return ArrayCollection|File[]
     */
    public function generate()
    {
        if ($this->isValid()) {
            foreach ($this->getFiles() as $file) {
                if ((!$file->isAuxFile() || !$file->isServiceFile()) &&
                    ($this->shouldOverwrite() && $file->isReadable())
                ) {
                    unlink($file->getRealPath());
                }

                $this->getTemplateStrategy()->renderFile($file);
            }

            return $this->getFiles();
        }

        return new ArrayCollection();
    }

    /**
     * Ensures that dependency files exist
     *
     * Certain objects we're generating declare their dependencies
     * on other objects. This ensures those dependencies exist.
     *
     * @param File $file
     * @return bool
     */
    protected function isDependencyValid(File $file)
    {
        if (!$file->isReadable()) {
            throw new \RuntimeException(sprintf(
                'Please ensure the file %s exists and is readable.',
                $file->getRealPath()
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
     * @param File $file
     * @return bool
     */
    protected function isFileValid(File $file)
    {
        if (file_exists($file->getRealPath()) &&
            (!$this->shouldOverwrite() && !$file->isAuxFile() && !$file->isServiceFile())
        ) {
            throw new \RuntimeException(sprintf(
                'Unable to generate the %s file as it already exists',
                $file->getRealPath()
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

        return $fields;
    }

    /**
     * Gets the short version of a Entity's FQDN
     *
     * Take an entity name and return the shortcut name
     * eg Acme\DemoBundle\Entity\Notes -> AcmeDemoBundle:Notes
     *
     * @param string $entity Fully qualified class name of the entity
     *
     * @return string
     */
    protected function getEntityBundleShortcut($entity)
    {
        $path = explode('\Entity\\', $entity);
        return str_replace('\\', '', $path[0]) . ':' . $path[1];
    }

    /**
     * @param ClassMetadata $metadata
     * @return mixed
     */
    protected function getEntityIdentifier(ClassMetadata $metadata)
    {
        if (count($metadata->identifier) !== 1) {
            throw new \InvalidArgumentException(
                'TdnPilotBundle is incompatible with entities that contain more than one identifier or no identifier.'
            );
        }

        return array_values($metadata->identifier)[0];
    }

    /**
     * Cleans properties with namespaces appended to them.
     *
     * This method always assumes that the directory for entities
     * will be `Entity` (symfony-standard). Will pop the last part of a string on
     * directory separators and assume it's the proper value.
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
