<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PhpTypes\Type\String;

/**
 * Class RoutingGenerator
 * @package Tdn\SfProjectGeneratorBundle\Generator
 */
class RoutingGenerator extends Generator
{
    /**
     * @var string
     */
    protected $routingFile;

    /**
     * @var string
     */
    protected $routingFileContents;

    /**
     * @var string
     */
    protected $route;

    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface $bundle The bundle in which to create the class
     * @param string $entity The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param array $options [restSupport => (bool)]
     *
     * @return bool
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, array $options = null)
    {
        $parts       = explode('\\', $this->routingFile);
        $this->routingFile = $bundle->getPath() ."/" . $options['routing-file'];
        $this->setGeneratedName(array_pop($parts));
        $this->setFilePath($this->routingFile);

        if (!file_exists($this->routingFile)) {
            $this->createConfig($this->routingFile);
        }

        $this->routingFileContents = file_get_contents($this->routingFile);
        $this->route = $this->getRouteFromEntity($entity, $options['prefix']);

        if (false === $options['remove']) {
            return $this->addConfiguration($bundle, $entity, $options['prefix']);
        } else {
            return $this->removeConfiguration($bundle, $entity);
        }
    }

    /**
     * @param $routingFile
     */
    protected function createConfig($routingFile)
    {
        try {
            if (!is_dir(dirname($routingFile))) {
                mkdir(dirname($routingFile));
            }

            touch($routingFile);
        } catch (\Exception $e) {
            throw new IOException('Could not create routing file ' . $routingFile . ' error: ' . $e->getMessage());
        }
    }

    /**
     * @param BundleInterface $bundle
     * @param string $prefix
     * @return string
     */
    protected function getRouteFromEntity($entity, $prefix)
    {
        return (string) String::create('api_')
            ->ensureRight(($prefix) ? $entity . '_' . (string) String::create($prefix)->replace('/', '') : $entity)
            ->toLowerCase();
    }

    /**
     * Generates the routing configuration.
     * @param BundleInterface $bundle
     * @param $entity
     * @param $prefix
     *
     * @return bool
     */
    protected function addConfiguration(BundleInterface $bundle, $entity, $prefix)
    {
        $output =
            sprintf("\n%s:\n", $this->getRouteFromEntity($entity, $prefix));
        $output .=
            sprintf("    resource: \"@%s/Controller/%sController.php\"\n    type:     rest\n", $bundle->getName(), $entity);
        $output .=
            sprintf("    prefix:   /%s\n", $prefix);
        $output .=
            sprintf("    defaults: {_format:%s}\n", 'json');
        $output .= "\n";

        if (false === file_put_contents($this->routingFile, $output, FILE_APPEND)) {
            return false;
        }

        return true;
    }

    /**
     * @param BundleInterface $bundle
     * @param $entity
     * @return bool
     */
    protected function removeConfiguration(BundleInterface $bundle, $entity)
    {
        //@todo Implement removeConfiguration
        return false;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $generatedName
     */
    public function setGeneratedName($generatedName)
    {
        $this->generatedName = $generatedName;
    }
}