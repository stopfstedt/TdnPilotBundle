<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;

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

        $handler->setContents($this->getHandlerFileContent());

        $this->addFile($handler);
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
        $serviceFile = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.%s',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getFormat()
            )
        );

        $serviceFile
            ->setContents($this->getServiceFileContents($serviceFile))
            ->setServiceFile(true)
        ;

        $this->addMessage(sprintf(
            'Make sure to load "%s" in the %s file to enable the new services.',
            $serviceFile->getFilename() . '.' . $serviceFile->getExtension(),
            $this->getDefaultExtensionFile()
        ));
        $this->addFile($serviceFile);
    }

    /**
     * Declares service and returns what the contents would be based on the format selected
     *
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

        $diUtils = $this->getServiceUtils();
        $diUtils->addParameter($paramKey, $serviceClass);
        $diUtils->addService($serviceId, $service);

        return $diUtils->getFormattedContents($this->getFormat());
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
