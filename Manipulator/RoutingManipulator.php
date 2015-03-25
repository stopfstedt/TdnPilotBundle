<?php

namespace Tdn\PilotBundle\Manipulator;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PilotBundle\Model\GeneratedFile;
use Tdn\PilotBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\PhpTypes\Type\String;

/**
 * Class RoutingGenerator
 * @package Tdn\PilotBundle\Manipulator
 */
class RoutingManipulator extends AbstractManipulator
{
    /**
     * @var string
     */
    private $routingFile;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var bool
     */
    private $remove;

    /**
     * @var bool
     */
    private $isInFile;

    /**
     */
    public function __construct()
    {
        $this->setRemove(false);
        $this->isInFile = false;

        parent::__construct();
    }

    /**
     * @param string $routingFile
     */
    public function setRoutingFile($routingFile)
    {
        $this->routingFile = $routingFile;
    }

    /**
     * @return string
     */
    public function getRoutingFile()
    {
        return $this->routingFile;
    }

    /**
     * @param string $routePrefix
     */
    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
    }

    /**
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * @param bool $remove
     */
    public function setRemove($remove)
    {
        $this->remove = $remove;
    }

    /**
     * @return bool
     */
    public function shouldRemove()
    {
        return $this->remove;
    }

    /**
     * Sets up routing file contents based on state for a specific entity (adding/removing).
     * @return $this
     */
    public function prepare()
    {
        list($file, $extension) = explode('.', $this->getRoutingFile());
        $routing = new GeneratedFile();
        $routing
            ->setFilename($file)
            ->setExtension($extension)
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            ))
            ->setContents($this->getRoutingContents($routing))
            ->setAuxFile(true) //Needed because this might exist but we still want to add routes to it.
        ;

        $this->addGeneratedFile($routing);
        $this->addControllerDependency();

        return $this;
    }

    /**
     * @param  GeneratedFile $routingFile
     * @return string
     */
    protected function getRoutingContents(GeneratedFile $routingFile)
    {
        $content = $routingFile->getContents() ?: '';
        $configurationText = $this->getConfigurationText();
        $this->isInFile    = $this->hasConfigurationText($content, $configurationText);

        if (false === $this->shouldRemove()) {
            //If we're not removing a route, but we're overwriting the file and the route exists, remove it then add it
            //No dupes.
            if ($this->shouldOverwrite() && $this->isInFile) {
                $content = $this->removeFromConfiguration($content, $configurationText);
            }

            $content = $this->addToConfiguration($content, $configurationText);
        } else {
            $content = $this->removeFromConfiguration($content, $configurationText);
        }

        return $content;
    }

    /**
     * @return string
     */
    protected function getRouteFromEntity()
    {
        return (string) String::create('api_')
            ->ensureRight(
                ($this->getRoutePrefix()) ?
                $this->getEntity() . '_' . (string) String::create($this->getRoutePrefix())->replace('/', '') :
                $this->getEntity()
            )
            ->toLowerCase();
    }

    /**
     * Generates the routing configuration.
     * @return string
     */
    protected function getConfigurationText()
    {
        $output = sprintf("%s:\n", $this->getRouteFromEntity());
        $output .=
            sprintf(
                "    resource: \"@%s/Controller/%sController.php\"\n    type:     rest\n",
                $this->getBundle()->getName(),
                $this->getEntity()
            );
        $output .=
            sprintf(
                "    prefix:   /%s\n",
                (string) String::create($this->getRoutePrefix())->replace('/', '')
            );
        $output .=
            sprintf("    defaults: {_format:%s}\n", 'json');

        return $output;
    }

    /**
     * @param string $content
     * @param string $toCheck
     * @throws IOException
     *
     * @return bool
     */
    protected function hasConfigurationText($content, $toCheck)
    {
        $contents = String::create($content);

        return $contents->contains($toCheck);
    }

    /**
     * @param string $content
     * @param string $toAdd
     *
     * @return string
     */
    protected function addToConfiguration($content, $toAdd)
    {
        return $content . $toAdd;
    }

    /**
     * @param string $content
     * @param string $toRemove
     * @throws IOException
     *
     * @return string
     */
    protected function removeFromConfiguration($content, $toRemove)
    {
        $content = String::create($content);
        if ($content->contains($toRemove)) {
            $newContent = (string) $content->subStrUntil($toRemove, true);
            $newContent .= (string) $content->subStrAfter($toRemove, true);

            return $newContent;
        }

        return (string) $content;
    }

    /**
     * @return void
     */
    protected function addControllerDependency()
    {
        $controllerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '%sController.php',
            $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new SplFileInfo($controllerFile, null, null));
    }

    /**
     * @throws \RuntimeException
     * @return bool
     */
    public function isValid()
    {
        //Make sure routes are added to the file only once.
        if (!$this->shouldOverwrite() && !$this->shouldRemove() && $this->isInFile) {
            throw new \RuntimeException('Route is already in file.');
        }

        return parent::isValid();
    }
}
