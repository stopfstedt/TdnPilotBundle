<?php

namespace Tdn\PilotBundle\Template\Strategy;

use Tdn\PilotBundle\Model\FileInterface;

/**
 * Interface TemplateStrategyInterface
 * @package Tdn\PilotBundle\Template\Strategy
 */
interface TemplateStrategyInterface
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
     * @param FileInterface $target
     */
    public function renderFile(FileInterface $target);
}
