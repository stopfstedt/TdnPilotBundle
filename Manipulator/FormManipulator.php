<?php

namespace Tdn\PilotBundle\Manipulator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Class FormManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class FormManipulator extends AbstractManipulator
{
    /**
     * Sets up a FormType based on entity.
     * Sets up InvalidFormException if it doesn't exist exists.
     * @return $this
     */
    public function prepare()
    {
        $this->addFormType();
        $this->addFormException();
        $this->addManagerDependency();

        return $this;
    }

    /**
     * @return void
     */
    protected function addFormType()
    {
        $formType = new File();
        $formType
            ->setFilename($this->getEntity() . 'Type')
            ->setExtension('php')
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            )) //<TargetDir>|<Bundle>/Form/Type
            ->setContents($this->generateFormTypeContent($formType->getFilename()))
        ;

        $this->addGeneratedFile($formType);
    }

    /**
     * @return void
     */
    protected function addFormException()
    {
        $formTypeException = new File();
        $formTypeException
            ->setFilename('InvalidFormException')
            ->setExtension('php')
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Exception',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            )) //<TargetDir>|<Bundle>/Exception
        ;

        //File created only once.
        if (!is_file($formTypeException->getFullPath()) || $this->shouldOverwrite()) {
            $formTypeException->setContents($this->generateFormTypeExceptionContent());
            $this->addGeneratedFile($formTypeException);
        }
    }

    /**
     * @param  string $fileName
     * @return string
     */
    protected function generateFormTypeContent($fileName)
    {
        return $this->getTemplateStrategy()->render(
            'form/FormType.php.twig',
            [
                'fields'                => $this->getFieldsFromMetadata($this->getMetadata()),
                'associations'          => $this->getMetadata()->associationMappings,
                'namespace'             => $this->getBundle()->getNamespace(),
                'entity_namespace'      => $this->getEntityNamespace(),
                'entity_class'          => $this->getEntity(),
                'bundle'                => $this->getBundle()->getName(),
                'entity_identifier'     => $this->getEntityIdentifier(),
                'form_class'            => String::create($fileName)->underscored()->toLowerCase(),
            ]
        );
    }

    /**
     * @return string
     */
    protected function generateFormTypeExceptionContent()
    {
        return $this->getTemplateStrategy()->render(
            'form/form_exception.php.twig',
            [
                'namespace' => $this->getBundle()->getNamespace()
            ]
        );
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

    /**
     * Find entity identifier.
     *
     * Figures out what an entity's identifier is from it's metadata
     * And returns the name of the identifier.
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    protected function getEntityIdentifier()
    {
        if (count($this->getMetadata()->getIdentifierFieldNames()) !== 1) {
            throw new \RuntimeException('Only one identifier allowed at this time.');
        }

        return $this->getMetadata()->getIdentifierFieldNames()[0];
    }
}
