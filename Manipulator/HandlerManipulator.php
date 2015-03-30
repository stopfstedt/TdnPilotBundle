<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\Finder\SplFileInfo;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\GeneratedFile;
use Tdn\PilotBundle\Model\GeneratedFileInterface;

/**
 * Class HandlerManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class HandlerManipulator extends AbstractServiceManipulator
{
    /**
     * Generates REST Handler based on entity.
     *
     * @return $this
     */
    public function prepare()
    {
        $path = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Handler',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
        );

        $handler = new GeneratedFile();
        $handler
            ->setFilename($this->getEntity() . 'Handler')
            ->setExtension('php')
            ->setPath($path)
            ->setContents($this->getHandlerFileContent())
        ;

        $this->addGeneratedFile($handler);
        $this->addHandlerServiceFile();
        $this->addManagerDependency();
        $this->addFormTypeDependency();
        $this->setUpdatingDiConfFile(true);

        return $this;
    }

    /**
     * @return string
     */
    protected function getHandlerFileContent()
    {
        return $this->getTemplateStrategy()->render('handler/handler.php.twig', [
            'entity' => $this->getEntity(),
            'namespace' => $this->getBundle()->getNamespace(),
        ]);
    }

    /**
     * @return void
     */
    protected function addHandlerServiceFile()
    {
        $serviceFile = new GeneratedFile();
        $serviceFile
            ->setFilename('handlers')
            ->setExtension($this->getFormat())
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            ))
            ->setContents($this->getServiceFileContents())
            ->setServiceFile(true)
        ;

        $this->addMessage(sprintf(
            'Make sure to load "%s" in the %s file to enable the new services.',
            $serviceFile->getFilename() . '.' . $serviceFile->getExtension(),
            $this->getDefaultExtensionFile()
        ));
        $this->addGeneratedFile($serviceFile);
    }

    /**
     * @return string
     */
    public function getServiceFileContents()
    {
        $serviceClass = sprintf(
            '%s\\Handler\\%sHandler',
            $this->getBundle()->getNamespace(),
            $this->getEntity()
        );

        $paramKey = sprintf(
            '%s.%s.handler.class',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', ''),
            strtolower($this->getEntity())
        );

        $serviceId = sprintf(
            '%s.%s.handler',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', ''),
            strtolower($this->getEntity())
        );

        $service = [
            'class' => '%' . $paramKey . '%',
            'arguments' => [
                '@doctrine',
                sprintf(
                    '%s\\Entity\\%s%s',
                    $this->getBundle()->getNamespace(),
                    $this->getEntityNamespace(),
                    $this->getEntity()
                ),
                '@form.factory'
            ]
        ];

        $diUtils = $this->getDiUtils();
        $diUtils->addParameter($paramKey, $serviceClass);
        $diUtils->addService($serviceId, $service);

        return $diUtils->getContentsInFormat($this->getFormat());
    }

    protected function addManagerDependency()
    {
        $managerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Entity' . DIRECTORY_SEPARATOR .
            'Manager' . DIRECTORY_SEPARATOR . '%sManager.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new SplFileInfo($managerFile, null, null));
    }

    protected function addFormTypeDependency()
    {
        $formType = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type' . DIRECTORY_SEPARATOR . '%sType.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new SplFileInfo($formType, null, null));
    }
}
