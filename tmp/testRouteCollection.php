<?php

include '../vendor/autoload.php';

use Symfony\Component\Yaml\Parser;
use Tdn\PilotBundle\Tests\Fixtures\RouteUtilsData;

$parser = new Parser();
$config = $parser->parse(RouteUtilsData::YAML);

var_dump($config);
