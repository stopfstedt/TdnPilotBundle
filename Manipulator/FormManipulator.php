<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;

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
        $formType = new File(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type' . DIRECTORY_SEPARATOR . '%sType.php',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getEntity()
            )
        );

        $formType->setFilteredContents($this->generateFormTypeContent($formType->getFilename()));
        $this->addFile($formType);
    }

    /**
     * @return void
     */
    protected function addFormException()
    {
        $formTypeException = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR . 'InvalidFormException.php',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            )
        );

        //File created only once.
        if (!is_file($formTypeException->isReadable()) || $this->shouldOverwrite()) {
            $formTypeException->setFilteredContents($this->getFormTypeExceptionContent());
            $this->addFile($formTypeException);
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
    protected function getFormTypeExceptionContent()
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

        $this->addFileDependency(new File($managerFile));
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
