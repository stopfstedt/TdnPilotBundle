<?php

namespace Tdn\PilotBundle\Tests\Template\Engine;

use Symfony\Component\Filesystem\Filesystem;
use Tdn\PilotBundle\Model\FileInterface;
use Tdn\PilotBundle\Template\Strategy\TwigStrategy;
use Tdn\PilotBundle\TdnPilotBundle;
use \Mockery;

/**
 * Class TwigStrategyTest
 * @package Tdn\PilotBundle\Tests\Template\Strategy
 */
class TwigStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $outDir;

    public function testRender()
    {
        $this->assertEquals('hello world', $this->getRendered());
    }

    public function testRenderFile()
    {
        $this->getOutputEngine()->renderFile($this->getFileMock());
        $this->assertTrue(file_exists($this->getFileMock()->getFullPath()));
        $this->assertEquals('hello world', file_get_contents($this->getFileMock()->getFullPath()));
    }

    protected function setUp()
    {
        $this->outDir       = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tdn-pilot';
        $this->filesystem   = new Filesystem();
        $this->filesystem->remove($this->outDir);
        $this->filesystem->mkdir($this->outDir);
    }

    /**
     * @return TwigStrategy
     */
    protected function getOutputEngine()
    {
        $outputEngine = new TwigStrategy();
        $outputEngine->setSkeletonDirs($this->getSkeletonDirs());

        return $outputEngine;
    }

    /**
     * @return string[]
     */
    protected function getSkeletonDirs()
    {
        $bundleClass    = new \ReflectionClass(new TdnPilotBundle());
        $skeletonDirs   = [];
        $skeletonDirs[] = dirname($bundleClass->getFileName()) . '/Tests/skeleton';

        return $skeletonDirs;
    }

    protected function getRendered()
    {
        return $this->getOutputEngine()->render('hello.txt.twig', [
            'hello_var' => 'hello world'
        ]);
    }

    /**
     * @return FileInterface
     */
    protected function getFileMock()
    {
        $file = Mockery::mock('\Tdn\PilotBundle\Model\GeneratedFile');
        $file
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getFilename'  => 'hello',
                    'getPath'      => $this->getOutDir(),
                    'getExtension' => 'txt',
                    'getContents'  => 'hello world',
                    'getFullPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR . 'hello.txt'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $file;
    }

    /**
     * @return string
     */
    protected function getOutDir()
    {
        return $this->outDir;
    }

    protected function tearDown()
    {
        $this->filesystem->remove($this->outDir);
        Mockery::close();
    }
}
