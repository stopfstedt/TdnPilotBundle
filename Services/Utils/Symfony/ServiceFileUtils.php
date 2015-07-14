<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Tuck\ConverterBundle\ConfigFormatConverter;
use Tuck\ConverterBundle\Dumper\StandardDumperFactory;
use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\Loader\StandardLoaderFactory;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Model\ServiceDefinition;

/**
 * Class ServiceFileUtils
 * @package Tdn\PilotBundle\ServiceFileLoader\Symfony
 */
class ServiceFileUtils extends AbstractFileUtils
{
    /**
     * @var ArrayCollection
     */
    protected $parameters;

    /**
     * @var ArrayCollection
     */
    protected $serviceDefinitions;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
        $this->serviceDefinitions = new ArrayCollection();
        $this->container = new ContainerBuilder();
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * @param ServiceDefinition $serviceDefinition
     *
     * @return $this
     */
    public function addServiceDefinition(ServiceDefinition $serviceDefinition)
    {
        $this->serviceDefinitions->add($serviceDefinition);

        return $this;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    public function dump(File $file)
    {
        $container = $this->getResolvedContainer($file);

        return $this->getDumperFactory()->createDumper($this->getFormat($file), $container)->dump();
    }

    /**
     * @param File $file
     *
     * @return ContainerBuilder
     */
    protected function getResolvedContainer(File $file)
    {
        if ($file->isFile() && $file->isReadable()) {
            $loader = $this->getLoaderFactory()->createFileLoader(
                $this->getFormat($file),
                $this->container,
                $file->getPath()
            );
            $loader->load($file->getBasename());
        }

        foreach ($this->parameters as $paramK => $paramV) {
            $this->container->setParameter($paramK, $paramV);
        }

        foreach ($this->serviceDefinitions as $serviceDefinition) {
            $this->container->setDefinition($serviceDefinition->getId(), $serviceDefinition->getDefinition());
        }

        return $this->container;
    }

    /**
     * @return ConfigFormatConverter
     */
    protected function getFormatConverter()
    {
        return new ConfigFormatConverter(
            $this->getLoaderFactory(),
            $this->getDumperFactory(),
            $this->getTempFileFactory()
        );
    }

    /**
     * @return StandardLoaderFactory
     */
    protected function getLoaderFactory()
    {
        return new StandardLoaderFactory();
    }

    /**
     * @return StandardDumperFactory
     */
    protected function getDumperFactory()
    {
        return new StandardDumperFactory();
    }

    /**
     * @return SysTempFileFactory
     */
    protected function getTempFileFactory()
    {
        return new SysTempFileFactory();
    }
}
