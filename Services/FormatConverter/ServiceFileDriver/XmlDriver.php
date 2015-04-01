<?php

namespace Tdn\PilotBundle\Services\DependencyInjection\Parser;

use Tdn\PilotBundle\Services\DependencyInjection\Parser\YamlParser;
use Tuck\ConverterBundle\ConfigFormatConverter;

/**
 * Class XmlParser
 * @package Tdn\PilotBundle\Services\FormatConverter\ServiceFileDriver
 */
class XmlParser implements ParserInterface
{
    /**
     * @var ConfigFormatConverter
     */
    private $formatConverter;

    /**
     * @var YamlParser
     */
    private $yamlParser;

    public function __construct(ConfigFormatConverter $formatConverter, YamlParser $yamlParser)
    {
        $this->formatConverter = $formatConverter;
        $this->yamlParser = $yamlParser;
    }

    /**
     * @param string $contents
     *
     * @return array
     */
    public function getDefinitions($contents)
    {
        $yamlStr = $this->formatConverter->convertString($contents, 'xml', 'yaml');
        return $this->yamlParser->getDefinitions($yamlStr);
    }
}
