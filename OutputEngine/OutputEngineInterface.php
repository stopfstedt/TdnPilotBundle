<?php

namespace Tdn\PilotBundle\OutputEngine;

use Tdn\PilotBundle\Model\GeneratedFileInterface;

/**
 * Interface OutputEngineInterface
 * @package Tdn\PilotBundle\TdnPilotBundle\OutputEngine
 */
interface OutputEngineInterface
{
    /**
     * @param array $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs(array $skeletonDirs);

    /**
     * @return array
     */
    public function getSkeletonDirs();

    /**
     * @param $template
     * @param $parameters
     *
     * @return string
     */
    public function render($template, $parameters);

    /**
     * @param GeneratedFileInterface $target
     */
    public function renderFile(GeneratedFileInterface $target);
}
