<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Symfony\Component\Filesystem\Exception\IOException;
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
     * The directories must be sorted from bundle specific to app specific
     * (e.g. <bundle-dir>/Resources, app/Resources/<bu
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
     * @param string $generatedName
     */
    public function setGeneratedName($generatedName)
    {
        $this->generatedName = $generatedName;
    }

    /**
     * @return string
     */
    public function getGeneratedName()
    {
        if (!$this->generatedName) {
            throw new \RuntimeException('Invalid method call. generated name is null.');
        }

        return $this->generatedName;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        if (!$this->filePath) {
            throw new \RuntimeException('Invalid method call. file path is null');
        }

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
            $multiTypes = array(
                ClassMetadataInfo::ONE_TO_MANY,
                ClassMetadataInfo::MANY_TO_MANY,
            );
            if (in_array($relation['type'], $multiTypes)) {
                $fields[$fieldName]['realtedType'] = 'many';
            } else {
                $fields[$fieldName]['realtedType'] = 'single';
            }

            $fields[$fieldName]['relatedEntityShrotcut'] =
                $this->getEntityBundleShortcut($fields[$fieldName]['targetEntity']);
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
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->skeletonDirs), [
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ]);

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
     * @param string $template
     * @param string $target
     * @param array $parameters
     * @param bool $overwrite
     *
     * @throws IOException
     * @return bool
     */
    protected function renderFile($template, $target, array $parameters, $overwrite = false)
    {
        $mode = ($overwrite) ? 0 : FILE_APPEND;

        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        if (false === file_put_contents($target, $this->render($template, $parameters), $mode)) {
            throw new IOException(srpintf(
                'Could not write file %s based on template %s.',
                $target,
                $template
            ));
        }

        return true;
    }

    /**
     * Take an entity name and return the shortcut name
     * eg Acme\DemoBundle\Entity\Notes -> AcemDemoBundle:Notes
     * @param string $entity fully qualified class name of the entity
     */
    protected function getEntityBundleShortcut($entity)
    {
        // wrap in EntityManager's Class Metadata function avoid problems with cached proxy classes
        $path = explode('\Entity\\', $entity);
        return str_replace('\\', '', $path[0]).':'.$path[1];
    }
}
