<?php

namespace Tdn\PilotBundle\Services\Utils\Parser;

use Symfony\Component\Yaml\Parser as BaseYamlParser;

/**
 * Wrapper around symfony parser to force an array return type.
 *
 * Note that docs (http://symfony.com/doc/current/components/yaml/introduction.html#reading-yaml-files)
 * say it returns an array, but it is not noted in the doc block or through an interface.
 *
 * Class YamlParser
 * @package Tdn\PilotBundle\Services\Utils\Parser
 */
class YamlParser extends BaseYamlParser implements ParserInterface
{
    /**
     * @param $contents
     *
     * @return array
     */
    public function getDefinitions($contents)
    {
        //We'll always be loading yaml so should return an array at the end
        return (array) $this->parse($contents);
    }
}
