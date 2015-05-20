<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tdn\PilotBundle\ClassLoader;

use Symfony\Component\ClassLoader\ClassMapGenerator as BaseClassMapGenerator;

/**
 * Class ClassMapGenerator
 * @package Tdn\PilotBundle\ClassLoader
 */
class ClassMapGenerator extends BaseClassMapGenerator
{
    /**
     * Iterate over all files in the given directory searching for classes.
     *
     * @param \Iterator|string $dir The directory to search in or an iterator
     * @param int $depth
     *
     * @return array A class map array
     */
    public static function createMap($dir, $depth = 0)
    {
        if (is_string($dir)) {
            $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            $dir->setMaxDepth($depth);
        }

        $map = array();

        /** @var \SplFileInfo $file */
        foreach ($dir as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getRealPath();

            if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $classes = self::findClasses($path);

            foreach ($classes as $class) {
                $map[$class] = $path;
            }
        }

        return $map;
    }

    /**
     * Extract the classes in the given file.
     *
     * @param string $path The file to check
     *
     * @return array The found classes
     */
    private static function findClasses($path)
    {
        $contents = file_get_contents($path);
        $tokens = token_get_all($contents);

        $classes = array();

        $namespace = '';
        for ($i = 0, $max = count($tokens); $i < $max; $i++) {
            $token = $tokens[$i];

            if (is_string($token)) {
                continue;
            }

            $class = '';

            switch ($token[0]) {
                case T_NAMESPACE:
                    $namespace = '';
                    // If there is a namespace, extract it
                    while (($t = $tokens[++$i]) && is_array($t)) {
                        if (in_array($t[0], array(T_STRING, T_NS_SEPARATOR))) {
                            $namespace .= $t[1];
                        }
                    }
                    $namespace .= '\\';
                    break;
                case T_CLASS:
                case T_INTERFACE:
                case SYMFONY_TRAIT:
                    // Find the classname
                    while (($t = $tokens[++$i]) && is_array($t)) {
                        if (T_STRING === $t[0]) {
                            $class .= $t[1];
                        } elseif ($class !== '' && T_WHITESPACE == $t[0]) {
                            break;
                        }
                    }

                    $classes[] = ltrim($namespace.$class, '\\');
                    break;
                default:
                    break;
            }
        }

        return $classes;
    }

}
