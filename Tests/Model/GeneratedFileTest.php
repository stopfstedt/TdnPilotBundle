<?php

namespace Tdn\PilotBundle\Tests\Model;

use Tdn\PilotBundle\Model\File;
use \RuntimeException;

/**
 * Class GeneratedFileTest
 * @package Tdn\PilotBundle\Tests\Model
 */
class GeneratedFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return File
     */
    protected function getGeneratedFile()
    {
        return new File();
    }

    public function testPath()
    {
        $generatedFile = $this->getGeneratedFile();
        $this->assertNull($generatedFile->getPath());
        $generatedFile->setPath('foo');
        $this->assertEquals('foo', $generatedFile->getPath());
    }

    public function testFilename()
    {
        $generatedFile = $this->getGeneratedFile();
        $this->assertNull($generatedFile->getFilename());
        $generatedFile->setFilename('bar');
        $this->assertEquals('bar', $generatedFile->getFilename());
    }

    public function testExtension()
    {
        $generatedFile = $this->getGeneratedFile();
        $this->assertNull($generatedFile->getExtension());
        $generatedFile->setExtension('php');
        $this->assertEquals('php', $generatedFile->getExtension());
    }

    public function testFullPath()
    {
        $generatedFile = $this->getGeneratedFile();

        $this->assertEmpty($generatedFile->getFullPath());

        $generatedFile->setPath('foo');
        $generatedFile->setFilename('bar');
        $generatedFile->setExtension('php');

        $this->assertEquals('foo/bar.php', $generatedFile->getFullPath());
    }

    /**
     * @expectedException              RuntimeException
     * @expectedExceptionMessageRegExp #not set#
     */
    public function testIsValid()
    {
        $generatedFile = $this->getGeneratedFile();
        $generatedFile->isValid();
    }

    public function testContents()
    {
        $this->assertNull($this->getGeneratedFile()->getPath());
    }
}
