<?php

namespace Tdn\PilotBundle\Manipulator;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;

/**
 * Abstract Class AbstractManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractManipulator implements ManipulatorInterface
{
    /**
     * @var OutputEngineInterface
     */
    private $outputEngine;

    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var ClassMetadataInfo
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
     * @var boolean
     */
    private $overwrite;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @param OutputEngineInterface $outputEngine
     * @param BundleInterface       $bundle
     * @param ClassMetadataInfo     $metadata
     */
    public function __construct(
        OutputEngineInterface $outputEngine,
        BundleInterface $bundle,
        ClassMetadataInfo $metadata
    ) {
        $this->generatedFiles   = new ArrayCollection();
        $this->fileDependencies = new ArrayCollection();
        $this->setOutputEngine($outputEngine);
        $this->setBundle($bundle);
        $this->setMetadata($metadata);
        $this->setOverwrite(false);

        if (count($this->getMetadata()->identifier) > 1) {
            throw new \RuntimeException(sprintf(
                'The %s does not support entity classes with multiple primary keys.',
                __CLASS__
            ));
        }
    }

    public function setOutputEngine(OutputEngineInterface $outputEngine)
    {
        $this->outputEngine = $outputEngine;
    }

    /**
     * @return OutputEngineInterface
     */
    public function getOutputEngine()
    {
        return $this->outputEngine;
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
        return $this->getMetadata()->getName();
    }

    /**
     * @return string
     */
    public function getEntityNamespace()
    {
        return $this->getMetadata()->namespace;
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
    public function hasOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * @throws \RunTimeException
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->getFileDependencies() as $fileDependency) {
            $this->isDependencyValid($fileDependency);
        }

        foreach ($this->getGeneratedFiles() as $generatedFile) {
            $this->isGeneratedFileValid($generatedFile);
        }

        return true;
    }

    /**
     * @return ArrayCollection|GeneratedFileInterface[]
     */
    public function generate()
    {
        foreach ($this->getGeneratedFiles() as $generatedFile) {
            $this->getOutputEngine()->renderFile($generatedFile);
        }

        return $this->getGeneratedFiles();
    }

    /**
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
     * @param GeneratedFileInterface $generatedFile
     * @return bool
     */
    protected function isGeneratedFileValid(GeneratedFileInterface $generatedFile)
    {
        if (file_exists($generatedFile->getFullPath()) &&
            (!$this->hasOverwrite() || !$generatedFile->hasForceNew())
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
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param  ClassMetadataInfo $metadata
     * @return array             $fields
     */
    protected function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fields = array_merge($metadata->fieldMappings, $metadata->associationMappings);

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            foreach ($metadata->identifier as $identifier) {
                unset($fields[$identifier]);
            }
        }

        $multiTypes = array(
            ClassMetadataInfo::ONE_TO_MANY,
            ClassMetadataInfo::MANY_TO_MANY,
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
}
