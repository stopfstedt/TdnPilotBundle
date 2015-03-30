<?php

namespace Tdn\PilotBundle\Template\Strategy;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * TemplateStrategy utilities
 *
 * Class TemplateStrategyUtils
 * @package Tdn\PilotBundle\Template\Strategy
 */
class TemplateStrategyUtils
{
    /**
     * Gets the bundle's skeleton directories
     *
     * The bundle includes a skeleton directory in `Resources/skeleton`
     * Any files in that directory can be overwritten by creating a
     * app/Resources/PilotBundle/skeleton directory from your project root or
     * src/<Bundle>/Resources/PilotBundle/skeleton
     *
     *
     * @param BundleInterface|null $bundle
     * @param string|null          $rootDir
     *
     * @return string[]
     */
    public function getDefaultSkeletonDirs(BundleInterface $bundle = null, $rootDir = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath() . '/Resources/PilotBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (null !== $rootDir && is_dir($dir = $rootDir . '/Resources/PilotBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $reflClass = new \ReflectionClass(new self());
        $skeletonDirs[] = dirname($reflClass->getFileName()) . '/../../Resources/skeleton';

        return $skeletonDirs;
    }
}
