<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\PilotBundle\Model\GeneratedFileInterface;
use Tdn\PilotBundle\Services\Utils\DiXmlUtils;

/**
 * Abstract Class AbstractServiceManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
abstract class AbstractServiceManipulator extends AbstractManipulator implements ServiceManipulatorInterface
{
    /**
     * @var DiXmlUtils
     */
    private $diUtils;

    /**
     * @var \SimpleXMLElement
     */
    private $xmlServiceFile;

    /**
     * @var bool
     */
    private $updatingDiFile;

    /**
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface           $bundle
     * @param ClassMetadata             $metadata
     */
    public function __construct(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata
    ) {
        $this->setDiUtils(new DiXmlUtils()); //Refactor later.

        parent::__construct($templateStrategy, $bundle, $metadata);
    }

    /**
     * @param DiXmlUtils $diUtils
     */
    public function setDiUtils(DiXmlUtils $diUtils)
    {
        $this->diUtils = $diUtils;
    }

    /**
     * @return DiXmlUtils
     */
    public function getDiUtils()
    {
        return $this->diUtils;
    }

    /**
     * @param GeneratedFileInterface|null $file
     */
    public function setXmlServiceFile(GeneratedFileInterface $file = null)
    {
        $loadContents = ($file && $file->getContents()) ?
            $file->getContents() : $this->getTemplateStrategy()->render('config/services.xml.twig', []);

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
     * @param string $output
     *
     * @return string
     */
    public function formatOutput($output)
    {
        return $this->getDiUtils()->formatOutput($output);
    }

    /**
     * @return string
     */
    protected function getDefaultExtensionFile()
    {
        return sprintf(
            '%s/DependencyInjection/%s.php',
            $this->getBundle()->getPath(),
            str_replace('Bundle', 'Extension', $this->getBundle()->getName())
        );
    }
}
