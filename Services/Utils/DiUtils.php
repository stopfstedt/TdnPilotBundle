<?php

namespace Tdn\PilotBundle\Services\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Yaml\Dumper as YamlDumper;
use Tuck\ConverterBundle\ConfigFormatConverter;
use Tdn\PilotBundle\Services\Utils\Parser\ParserInterface;
use Tdn\PilotBundle\Services\Utils\Parser\YamlParser;
use Tdn\PilotBundle\Services\Utils\Parser\XmlParser;

class DiUtils
{
    /**
     * @var ArrayCollection
     */
    protected $parameters;

    /**
     * @var ArrayCollection
     */
    protected $services;

    /**
     * @vars ArrayCollection
     */
    protected $imports;

    /**
     * @var ConfigFormatConverter
     */
    protected $formatConverter;

    /**
     * @return array
     */
    public static function getSupportedExtensions()
    {
        return [
            'yml',
            'yaml',
            'xml'
        ];
    }

    public function __construct(ConfigFormatConverter $formatConverter)
    {
        $this->parameters = new ArrayCollection();
        $this->services   = new ArrayCollection();
        $this->imports    = new ArrayCollection();
        $this->formatConverter = $formatConverter;
    }

    /**
     * @param Collection $parameters
     */
    public function setParameters(Collection $parameters)
    {
        $this->parameters = new ArrayCollection();

        foreach ($parameters as $key => $value) {
            $this->addParameter($key, $value);
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function addParameter($key, $value)
    {
        $this->parameters->set($key, $value);
    }

    /**
     * @return ArrayCollection|string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param Collection $services
     */
    public function setServices(Collection $services)
    {
        $this->services = new ArrayCollection();

        foreach ($services as $id => $service) {
            $this->addService($id, $service);
        }
    }

    /**
     * @param $id
     * @param array $service
     */
    public function addService($id, array $service)
    {
        $this->services->set($id, $service);
    }

    /**
     * @return ArrayCollection|string[]
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param Collection $imports
     */
    public function setImports(Collection $imports)
    {
        $this->imports = new ArrayCollection();

        foreach ($imports as $import) {
            $this->addImport($import);
        }
    }

    /**
     * @param $import
     */
    public function addImport($import)
    {
        $this->imports->add($import);
    }

    /**
     * @return ArrayCollection|string[]
     */
    public function getImports()
    {
        return $this->imports;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->formatConverter->convertString($this->getYaml(), 'yml', 'xml');
    }

    /**
     * @return string
     */
    public function getYaml()
    {
        $yaml['parameters'] = $this->getParameters()->toArray();
        $yaml['services'] = $this->getServices()->toArray();

        return (new YamlDumper())->dump($yaml, 3);
    }

    /**
     * Saves to a file.
     *
     * The function extrapolates the format of the file (xml, yaml) based on the extension.
     *
     * @param string $file the file to save to.
     *
     * @return bool
     */
    public function save($file)
    {
        if (false === file_put_contents($file, $this->getData($this->getFormat($file)))) {
            throw new \RuntimeException(
                sprintf(
                    'Failed updating the %s file.',
                    $file
                )
            );
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function load($file)
    {
        try {
            $parser = $this->getParser($this->getFormat($file));
            $definitions = $parser->getDefinitions(file_get_contents($file));
            $this->extractParameters($definitions);
            $this->extractServices($definitions);
            $this->extractImports($definitions);

            return true;
        } catch (\Exception $e) {
            throw new IOException(
                sprintf(
                    'Could not parse and extract definitions from file %s.',
                    $file
                )
            );
        }
    }

    protected function getFormat($file)
    {
        $file = new \SplFileInfo($file);

        if (!in_array(strtolower($file->getExtension()), self::getSupportedExtensions())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid format. Expected one of %s, got %s.',
                    implode(',', self::getSupportedExtensions()),
                    $file->getExtension()
                )
            );
        }

        return null;
    }

    /**
     * @param $format
     * @return ParserInterface
     */
    protected function getParser($format)
    {
        switch (strtolower($format)) {
            case 'yaml':
            case 'yml':
                return new YamlParser();
            case 'xml':
                //I really don't want to deal with XML so let's just deal with Yaml internally.
                //As noted in http://stackoverflow.com/a/6943363/1583093 trying to create a one to one is not
                //ideal.
                return new XmlParser($this->formatConverter, new YamlParser());
            default:
                throw new \InvalidArgumentException('Invalid format passed: ' . $format);
        }
    }

    /**
     * @param $format
     * @return string
     */
    protected function getData($format)
    {
        switch (strtolower($format)) {
            case 'yaml':
            case 'yml':
                return $this->getYaml();
            case 'xml':
                return $this->getXml();
            default:
                throw new \InvalidArgumentException('Invalid format passed: ' . $format);
        }
    }

    /**
     * @param array $definitions
     */
    protected function extractParameters(array $definitions)
    {
        if (isset($definitions['parameters'])) {
            foreach ($definitions['parameters'] as $k => $v) {
                $this->addParameter($k, $v);
            }
        }
    }

    /**
     * @param array $definitions
     */
    protected function extractServices(array $definitions)
    {
        if (isset($definitions['services'])) {
            foreach ($definitions['services'] as $k => $v) {
                $this->addService($k, $v);
            }
        }
    }

    /**
     * @param array $definitions
     */
    protected function extractImports(array $definitions)
    {
        if (isset($definitions['imports'])) {
            foreach ($definitions['imports'] as $import) {
                //@todo: define import methods
            }
        }
    }
}
