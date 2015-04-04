<?php

namespace Tdn\PilotBundle\Tests\Template\Strategy;

use Tdn\PilotBundle\Template\Strategy\TemplateStrategyUtils;

/**
 * Class TemplateStrategyUtils
 * @package Tdn\PilotBundle\Tests\Template\Strategy
 */
class TemplateStrategyUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateStrategyUtils
     */
    protected $templateStrategyUtils;

    protected function setUp()
    {
        $this->templateStrategyUtils = new TemplateStrategyUtils();
    }

    public function testDefaultSkeletonDirs()
    {
        $this->assertEquals($this->getDefaultSkeletonDirs(), $this->templateStrategyUtils->getDefaultSkeletonDirs());
    }

    protected function getDefaultSkeletonDirs()
    {
        $reflClass = new \ReflectionClass(new self());

        return [realpath(dirname($reflClass->getFileName()) . '/../../../Resources/skeleton')];
    }
}
