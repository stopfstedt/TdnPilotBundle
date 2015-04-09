<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Loader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Tuck\ConverterBundle\Exception\UnknownFormatException;

/**
 * Class StandardLoaderFactory
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Loader
 */
class StandardLoaderFactory implements LoaderFactoryInterface
{
    protected $loaderMap = [
        'xml'  => '\Symfony\Component\Routing\Loader\XmlFileLoader',
        'yaml' => '\Symfony\Component\Routing\Loader\YamlFileLoader',
        'yml'  => '\Symfony\Component\Routing\Loader\YamlFileLoader'
    ];

    /**
     * @param string $type
     * @param string $path
     *
     * @return LoaderInterface
     *
     * @throws UnknownFormatException
     */
    public function getLoader($type, $path)
    {
        $class = $this->getClassFromType($type);

        return new $class(new FileLocator($path));
    }

    /**
     * @param string $type
     *
     * @return string
     *
     * @throws UnknownFormatException
     */
    protected function getClassFromType($type)
    {
        if (!isset($this->loaderMap[$type])) {
            throw UnknownFormatException::create($type);
        }

        return $this->loaderMap[$type];
    }
}
