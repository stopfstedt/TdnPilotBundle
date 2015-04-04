<?php

namespace Tdn\PilotBundle\Tests\Model;

use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Model\File;

/**
 * Class FileTest
 * @package Tdn\PilotBundle\Tests\Model
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var File
     */
    protected $file;

    protected function setUp()
    {
        $this->file = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('file') . '.php');
    }

    public function testInstanceOf()
    {
        $this->assertTrue($this->file instanceof SplFileInfo);
    }

    public function testContents()
    {
        $this->file->openFile('w')->fwrite('test');
        $this->assertEquals('test', $this->file->getContents());
        $this->assertTrue($this->file->isFile());
    }

    public function testFilteredContents()
    {
        $this->file->setFilteredContents('Esto es una prueba ;)');
        $this->assertEquals('Esto es una prueba ;)', $this->file->getFilteredContents());
    }

    public function testAuxFile()
    {
        $this->assertFalse($this->file->isAuxFile());
        $this->file->setAuxFile(true);
        $this->assertTrue($this->file->isAuxFile());
    }

    public function testServiceFile()
    {
        $this->assertFalse($this->file->isServiceFile());
        $this->file->setServiceFile(true);
        $this->assertTrue($this->file->isServiceFile());
    }

    protected function tearDown()
    {
        if ($this->file->isFile()) {
            unlink($this->file->getRealPath());
        }

        $this->file = null;
    }
}
