<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\Finder\SplFileInfo;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\GeneratedFile;

/**
 * Class FormManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class FormManipulator extends AbstractManipulator
{
    /**
     * @var boolean
     */
    private $restSupport;

    /**
     * @param bool $restSupport
     */
    public function setRestSupport($restSupport)
    {
        $this->restSupport = $restSupport;
    }

    /**
     * @return bool
     */
    public function hasRestSupport()
    {
        return $this->restSupport;
    }

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
        $formType = new GeneratedFile();
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
        $formTypeException = new GeneratedFile();
        $formTypeException
            ->setFilename('InvalidFormException')
            ->setExtension('php')
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Exception',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            )) //<TargetDir>|<Bundle>/Exception
        ;

        //File created only once.
        if (!is_file($formTypeException->getFullPath()) || $this->hasOverwrite()) {
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
        return $this->getOutputEngine()->render(
            'form/FormType.php.twig',
            [
                'fields'                => $this->getFieldsFromMetadata($this->getMetadata()),
                'associations'          => $this->getMetadata()->associationMappings,
                'namespace'             => $this->getBundle()->getNamespace(),
                'entity_namespace'      => $this->getEntityNamespace(),
                'entity_class'          => $this->getEntity(),
                'bundle'                => $this->getBundle()->getName(),
                'rest_support'          => $this->hasRestSupport(),
                'form_class'            => $fileName,
                'rest_form_type_name'   => (string) String::create($fileName)->toLowerCase(),
                'form_type_name'        =>
                    (string) String::create($this->getBundle()->getNamespace() . '_' . $fileName)
                        ->replace('\\', '_')
                        ->toLowerCase()
            ]
        );
    }

    /**
     * @return string
     */
    protected function generateFormTypeExceptionContent()
    {
        return $this->getOutputEngine()->render(
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
}
