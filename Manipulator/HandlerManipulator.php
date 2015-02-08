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
     * @return void
     */
    protected function addHandlerServiceFile()
    {
        $serviceFile = new GeneratedFile();
        $serviceFile
            ->setFilename('handlers')
            ->setExtension('xml')
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            ))
            ->setContents($this->getServiceFileContents($serviceFile)) //Kinda bad...fix later (Needs to be called last)
        ;

        $this->addGeneratedFile($serviceFile);
    }

    /**
     * @return string
     */
    protected function getHandlerFileContent()
    {
        return $this->getOutputEngine()->render('handler/handler.php.twig', [
            'entity' => $this->getEntity(),
            'namespace' => $this->getBundle()->getNamespace(),
        ]);
    }

    /**
     * @param GeneratedFileInterface $handlerFile
     *
     * @return string
     */
    public function getServiceFileContents(GeneratedFileInterface $handlerFile)
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

        $this->setXmlServiceFile($handlerFile);
        $newXml = $this->getXmlServiceFile();
        $this->getDiManipulator()->setDiXmlTags($newXml, $serviceClass, $paramKey, $serviceId);
        $service = $this->getDiManipulator()->getDiXmlServiceTag($serviceId, $newXml);
        $this->getDiManipulator()->addEmArgTo($service);
        $this->getDiManipulator()->addClassArgTo(
            $service,
            $this->getBundle()->getNamespace(),
            $this->getEntityNamespace(),
            $this->getEntity()
        );

        $formFactoryArgTag = $service->addChild('argument');
        $formFactoryArgTag->addAttribute('type', 'service');
        $formFactoryArgTag->addAttribute('id', 'form.factory');

        return $this->formatOutput(($newXml->asXML()) ?: '');
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
