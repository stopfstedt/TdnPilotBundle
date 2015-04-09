<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony;

use Symfony\Component\Routing\RouteCollection;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\StandardDumperFactory;
use Tdn\PilotBundle\Services\Utils\Symfony\Routing\Loader\StandardLoaderFactory;

class RoutingFileUtils extends AbstractFileUtils
{
    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    public function __construct()
    {
        $this->routeCollection = new RouteCollection();
    }

    /**
     * @param RouteCollection $routeCollection
     */
    public function addCollection(RouteCollection $routeCollection)
    {
        $this->routeCollection->addCollection($routeCollection);
    }

    /**
     * @param File $file
     * @return string
     */
    public function dump(File $file)
    {
        $routeCollection = $this->getResolvedRoutes($file);

        return $this->getDumperFactory()->createDumper($this->getFormat($file), $routeCollection)->dump();
    }

    /**
     * @return StandardDumperFactory
     */
    protected function getDumperFactory()
    {
        return new StandardDumperFactory();
    }

    /**
     * @return StandardLoaderFactory
     */
    protected function getLoaderFactory()
    {
        return new StandardLoaderFactory();
    }

    /**
     * @param File $file
     * @return RouteCollection
     */
    protected function getResolvedRoutes(File $file)
    {
        $routeCollection = new RouteCollection();

        if ($file->isFile() && $file->isReadable()) {
            $loader = $this->getLoaderFactory()->createLoader(
                $this->getFormat($file),
                $file->getPath()
            );

            $routeCollection = $loader->load($file->getBasename());
        }

        foreach ($this->routeCollection as $collection) {
            $routeCollection->addCollection($collection);
        }

        return $routeCollection;
    }
}
