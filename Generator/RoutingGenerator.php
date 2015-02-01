<?php

namespace Tdn\SfProjectGeneratorBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @param ArrayCollection $options [restSupport => (bool)]
     *
     * @return bool
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, ArrayCollection $options = null)
    {
        $this->routingFile = $bundle->getPath() ."/" . $options->get('routing-file');
        $parts = explode(DIRECTORY_SEPARATOR, $this->routingFile);
        $this->setGeneratedName(array_pop($parts));
        $this->setFilePath($this->routingFile);

        if (!file_exists($this->routingFile)) {
            $this->createConfigFile($this->routingFile);
        }

        $this->route = $this->getRouteFromEntity($entity, $options->get('prefix'));

        $output = $this->getConfigurationText($bundle, $entity, $options->get('prefix'));

        if (!$options->get('overwrite') && !$options->get('remove') && $this->inFile($output)) {
            throw new \RuntimeException('Route is already in file.');
        }

        if (false === $options->get('remove')) {
            if ($options->get('overwrite')) {
                $this->removeConfiguration($output);
            }
            $this->addConfiguration($output);
        } else {
            $this->removeConfiguration($output);
        }
    }

    /**
     * @param $routingFile
     */
    protected function createConfigFile($routingFile)
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
     * @param string $entity
     * @param string $prefix
     *
     * @return string
     */
    protected function getConfigurationText(BundleInterface $bundle, $entity, $prefix)
    {
        $output =
            sprintf("\n%s:\n", $this->getRouteFromEntity($entity, $prefix));
        $output .=
            sprintf("    resource: \"@%s/Controller/%sController.php\"\n    type:     rest\n", $bundle->getName(), $entity);
        $output .=
            sprintf("    prefix:   /%s\n", $prefix);
        $output .=
            sprintf("    defaults: {_format:%s}\n", 'json');

        return $output;
    }

    /**
     * @param $toCheck
     * @return bool
     */
    protected function inFile($toCheck)
    {
        $contents = null;
        if (false == $contents = file_get_contents($this->routingFile)) {
            throw new IOException('Error reading routing file.');
        }

        $contents = String::create($contents);
        if ($contents->contains($toCheck)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $output
     * @param bool   $overwrite
     * @return bool
     */
    protected function addConfiguration($output, $overwrite = false)
    {
        $mode = ($overwrite) ? 0 : FILE_APPEND;
        if (false === file_put_contents($this->routingFile, $output, $mode)) {
            return false;
        }

        return true;
    }

    /**
     * Probably a better way to do this but I have the flu and i'm tired :P
     * @param $toRemove
     * @return bool
     */
    protected function removeConfiguration($toRemove)
    {
        $contents = null;
        if (false == $contents = file_get_contents($this->routingFile)) {
            throw new IOException('Error reading routing file.');
        }

        $contents = String::create($contents);
        if ($contents->contains($toRemove)) {
            $newContent = (string)$contents->subStrUntil($toRemove, true);
            $newContent .= (string)$contents->subStrAfter($toRemove, true);
            $this->addConfiguration($newContent, true);
        }
    }
}
