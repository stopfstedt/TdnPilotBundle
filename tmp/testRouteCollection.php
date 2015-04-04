<?php

include '../vendor/autoload.php';

use Symfony\Component\Yaml\Parser;
use Tdn\PilotBundle\Tests\Fixtures\RouteUtilsData;

$parser = new Parser();
$yml    = RouteUtilsData::YAML;

$config = $parser->parse($yml);

var_dump($config);
