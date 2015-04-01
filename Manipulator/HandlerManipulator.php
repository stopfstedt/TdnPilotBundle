<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\DependencyInjection\Definition;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Model\Format;
use Tdn\PilotBundle\Model\ServiceDefinition;

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
        $handler = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . '%sHandler.php',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getEntity()
            )
        );

        $handler->setFilteredContents($this->getHandlerFileContent());

        $this->addFile($handler);
        $this->addManagerDependency();
        $this->addFormTypeDependency();

        if ($this->getFormat() !== Format::ANNOTATION) {
            $this->addHandlerServiceFile();
        }

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
        $serviceFile = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.%s',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getFormat()
            )
        );

        $serviceFile
            ->setFilteredContents($this->getServiceFileContents($serviceFile))
            ->setServiceFile(true)
        ;

        $this->addMessage(sprintf(
            'Make sure to load "%s" in your extension file to enable the new services.',
            $serviceFile->getBasename()
        ));

        $this->addFile($serviceFile);
    }

    /**
     * Declares service and returns what the contents would be based on the format selected
     *
     * @param File $file
     *
     * @return string
     */
    public function getServiceFileContents(File $file)
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

        $definition = new Definition('%'. $paramKey . '%');
        $definition
            ->addArgument('@doctrine')
            ->addArgument(
                sprintf(
                    '%s\\Entity\\%s%s',
                    $this->getBundle()->getNamespace(),
                    $this->getEntityNamespace(),
                    $this->getEntity()
                )
            )
            ->addArgument('@form.factory')
        ;

        return $this->getServiceFileUtil()
            ->addParameter($paramKey, $serviceClass)
            ->addServiceDefinition(new servicedefinition($serviceId, $definition))
            ->dump($file)
        ;
    }

    /**
     * @return void
     */
    protected function addManagerDependency()
    {
        $managerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Entity' . DIRECTORY_SEPARATOR .
            'Manager' . DIRECTORY_SEPARATOR . '%sManager.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new File($managerFile));
    }

    /**
     * @return void
     */
    protected function addFormTypeDependency()
    {
        $formType = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type' . DIRECTORY_SEPARATOR . '%sType.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new File($formType));
    }
}
