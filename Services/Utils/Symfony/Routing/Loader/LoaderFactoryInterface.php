<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Loader;

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Interface LoaderFactoryInterface
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Loader
 */
interface LoaderFactoryInterface
{
    /**
     * @param string $type
     * @param string $path
     *
     * @return LoaderInterface
     */
    public function createLoader($type, $path);
}
