<?php

namespace Tdn\PilotBundle\Services\Utils\Parser;

use Tdn\PilotBundle\Services\Utils\Parser\YamlParser;
use Tuck\ConverterBundle\ConfigFormatConverter;

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
