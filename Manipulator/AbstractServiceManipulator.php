<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\Services\Utils\DependencyInjection\DiXmlManipulator;

/**
 * Abstract Class AbstractServiceManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractServiceManipulator extends AbstractManipulator implements ServiceManipulatorInterface
{
    /**
     * @var string
     */
    private $extensionFile;

    /**
     * @var bool
     */
    private $updatingDiFile;

    /**
     * @var DiXmlManipulator
     */
    private $diManipulator;

    /**
     * @var \SimpleXMLElement
     */
    private $xmlServiceFile;

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
        $this->setDiManipulator(new DiXmlManipulator()); //Refactor later.

        parent::__construct($outputEngine, $bundle, $metadata);
    }

    /**
     * @param GeneratedFileInterface|null $file
     */
    public function setXmlServiceFile(GeneratedFileInterface $file = null)
    {
        $loadContents = ($file && $file->getContents()) ?
            $file->getContents() : $this->getOutputEngine()->render('config/services.xml.twig', []);

        $this->xmlServiceFile = simplexml_load_string($loadContents);
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getXmlServiceFile()
    {
        if (!$this->xmlServiceFile) {
            $this->setXmlServiceFile();
        }

        return $this->xmlServiceFile;
    }

    /**
     * @param string|null $extensionFile
     */
    public function setExtensionFile($extensionFile = null)
    {
        $this->extensionFile = ($extensionFile) ?: sprintf(
            '%s/DependencyInjection/%s.php',
            $this->getBundle()->getPath(),
            str_replace('Bundle', 'Extension', $this->getBundle()->getName())
        );
    }

    /**
     * @return string
     */
    public function getExtensionFile()
    {
        if (null === $this->extensionFile) {
            $this->setExtensionFile();
        }

        return $this->extensionFile;
    }

    /**
     * @param bool $updatingDiFile
     */
    public function setUpdatingDiConfFile($updatingDiFile)
    {
        $this->updatingDiFile = $updatingDiFile;
    }

    /**
     * @return bool
     */
    public function isUpdatingDiConfFile()
    {
        return $this->updatingDiFile;
    }

    /**
     * @param DiXmlManipulator $diManipulator
     */
    public function setDiManipulator(DiXmlManipulator $diManipulator)
    {
        $this->diManipulator = $diManipulator;
    }

    /**
     * @return DiXmlManipulator
     */
    public function getDiManipulator()
    {
        return $this->diManipulator;
    }

    /**
     * @param string $output
     *
     * @return string
     */
    public function formatOutput($output)
    {
        return $this->getDiManipulator()->formatOutput($output);
    }

    /**
     * @return ArrayCollection|GeneratedFileInterface[]
     */
    public function generate()
    {
        foreach ($this->getGeneratedFiles() as $generatedFile) {
            $this->getOutputEngine()->renderFile($generatedFile);
            if ($this->isUpdatingDiConfFile()) {
                $this->getDiManipulator()->updateDiFile(
                    $this->getExtensionFile(),
                    $generatedFile->getFilename() . '.' . $generatedFile->getExtension()
                );
            }
        }

        return $this->getGeneratedFiles();
    }
}
