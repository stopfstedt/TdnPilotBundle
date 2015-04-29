<?php

namespace Tdn\PilotBundle\Tests\Services\Symfony\Routing\Dumper;

use Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\StandardDumperFactory;

/**
 * Class StandardDumperFactoryTest
 * @package Tdn\PilotBundle\Tests\Services\Symfony\Routing\Dumper
 */
class StandardDumperFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $standardDumperFactory;

    protected function setUp()
    {
        $this->standardDumperFactory = new StandardDumperFactory();
    }

    public function testSupportedFormats()
    {

    }
}
