<?php

namespace Tdn\PilotBundle\Tests\Model;

use Tdn\PilotBundle\Model\File;
use \RuntimeException;

/**
 * Class FileTest
 * @package Tdn\PilotBundle\Tests\Model
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return File
     */
    protected function getGeneratedFile()
    {
        return new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . "test");
    }

    public function testContents()
    {

    }

    public function testAuxFile()
    {

    }

    public function testServiceFile()
    {

    }

    public function save()
    {

    }
}
