<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PhpTypes\Type\String;

/**
 * Class Generator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
abstract class Generator implements GeneratorInterface
{
    /**
     * @var BundleInterface
     */
    protected $bundle;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var ClassMetadataInfo
     */
    protected $metadata;

    /**
     * @var array
     */
    protected $skeletonDirs;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $generatedName;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * Sets an array of directories to look for templates.
     *
     * The directories must be sorted from bundle specific to app specific (e.g. <bundle-dir>/Resources, app/Resources/<bu
     *
     * @param array $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs(array $skeletonDirs)
    {
        $this->skeletonDirs = $skeletonDirs;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getGeneratedName()
    {
        return $this->generatedName;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
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

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                unset($fields[$fieldName]);
            }
        }

        return $fields;
    }

    /**
     * @param $template
     * @param $parameters
     *
     * @return string
     */
    protected function render($template, $parameters)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->skeletonDirs), array(
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ));

        $twig->addFilter(new \Twig_SimpleFilter('addslashes', 'addslashes'));
        $twig->addFilter(new \Twig_SimpleFilter('lowerfirst', function($input) {
            return (string) String::create($input)->lowerCaseFirst();
        }));
        $twig->addFilter(new \Twig_SimpleFilter('pluralize', function($input) {
            return (string) String::create($input)->pluralize();
        }));

        return $twig->render($template, $parameters);
    }

    /**
     * @param $template
     * @param $target
     * @param $parameters
     *
     * @return int
     */
    protected function renderFile($template, $target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->render($template, $parameters), FILE_APPEND);
    }
}
