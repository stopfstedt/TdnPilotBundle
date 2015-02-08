<?php

namespace Tdn\PilotBundle\Template\Strategy;

use Symfony\Component\Filesystem\Exception\IOException;
use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\GeneratedFileInterface;

/**
 * Class TwigStrategy
 * @package Tdn\PilotBundle\Template\Strategy
 */
class TwigStrategy implements TemplateStrategyInterface
{
    /**
     * @var array
     */
    private $skeletonDirs;

    /**
     * @param array $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs(array $skeletonDirs)
    {
        $this->skeletonDirs = $skeletonDirs;
    }

    /**
     * @return array
     */
    public function getSkeletonDirs()
    {
        return $this->skeletonDirs;
    }

    /**
     * @param $template
     * @param $parameters
     *
     * @return string
     */
    public function render($template, $parameters)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->getSkeletonDirs()), [
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ]);

        $twig->addFilter(new \Twig_SimpleFilter('addslashes', 'addslashes'));
        $twig->addFilter(new \Twig_SimpleFilter(
            'lowerfirst',
            function ($input) {
                return (string) String::create($input)->lowerCaseFirst();
            }
        ));
        $twig->addFilter(new \Twig_SimpleFilter(
            'pluralize',
            function ($input) {
                return (string) String::create($input)->pluralize();
            }
        ));

        return $twig->render($template, $parameters);
    }

    /**
     * @param GeneratedFileInterface $target
     *
     * @throws IOException
     */
    public function renderFile(GeneratedFileInterface $target)
    {
        if (!is_dir(dirname($target->getFullPath()))) {
            mkdir(dirname($target->getFullPath()), 0755, true);
        }

        //Always recreate
        //Used specifically for auxiliary files (e.g. interfaces) and not the main file that the manipulator generates.
        //See controller manipulator or manager manipulator for example.
        if ($target->isServiceFile() || $target->isAuxFile()) {
            if (file_exists($target->getFullPath())) {
                unlink($target->getFullPath()); //Remove before recreating
            }
        }

        if (false === file_put_contents($target->getFullPath(), $target->getContents())) {
            throw new IOException(sprintf(
                'Could not write file %s.',
                $target->getFullPath()
            ));
        }
    }
}
