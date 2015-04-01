<?php

namespace Tdn\PilotBundle\Services\FormatConverter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Dumper as YamlDumper;
use Tdn\PilotBundle\Model\Format;
use Tuck\ConverterBundle\ConfigFormatConverter;
use Tdn\PilotBundle\Services\FormatConverter\ServiceFileDriver\DriverInterface;
use Tdn\PilotBundle\Services\FormatConverter\ServiceFileDriver\YamlDriver;
use Tdn\PilotBundle\Services\FormatConverter\ServiceFileDriver\XmlDriver;

/**
 * Class ServiceFileConverter
 * @package Tdn\PilotBundle\Services\FormatConverter
 */
class ServiceFileConverter
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
     * @var SplFileInfo
     */
    protected $file;

    /**
     * @return array
     */
    public static function getSupportedExtensions()
    {
        return [
            Format::YAML,
            Format::YML,
            Format::XML
        ];
    }

    public function __construct(ConfigFormatConverter $formatConverter, SplFileInfo $file = null)
    {
        $this->setFormatConverter($formatConverter);
        $this->parameters      = new ArrayCollection();
        $this->services        = new ArrayCollection();
        $this->imports         = new ArrayCollection();
        if ($file) {
            $this->setFile($file);
        }
    }

    /**
     * @param Collection $parameters
     *
     * @return $this
     */
    public function setParameters(Collection $parameters)
    {
        $this->parameters = new ArrayCollection();

        foreach ($parameters as $key => $value) {
            $this->addParameter($key, $value);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
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
     *
     * @return $this
     */
    public function setServices(Collection $services)
    {
        $this->services = new ArrayCollection();

        foreach ($services as $id => $service) {
            $this->addService($id, $service);
        }

        return $this;
    }

    /**
     * @param $id
     * @param array $service
     *
     * @return $this
     */
    public function addService($id, array $service)
    {
        $this->services->set($id, $service);

        return $this;
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
     *
     * @return $this
     */
    public function setImports(Collection $imports)
    {
        $this->imports = new ArrayCollection();

        foreach ($imports as $import) {
            $this->addImport($import);
        }

        return $this;
    }

    /**
     * @param array $import
     *
     * @return $this
     */
    public function addImport(array $import)
    {
        if (!array_key_exists('resource', $import)) {
            throw new \InvalidArgumentException(
                'Invalid import. Import array expects key named "resource" with a value of <string>.'
            );
        }

        $this->imports->add($import);

        return $this;
    }

    /**
     * @return ArrayCollection|string[]
     */
    public function getImports()
    {
        return $this->imports;
    }

    /**
     * @param ConfigFormatConverter $formatConverter
     *
     * @return $this
     */
    public function setFormatConverter(ConfigFormatConverter $formatConverter)
    {
        $this->formatConverter = $formatConverter;

        return $this;
    }

    /**
     * @return ConfigFormatConverter
     */
    public function getFormatConverter()
    {
        return $this->formatConverter;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return $this
     */
    public function setFile(SplFileInfo $file)
    {
        $this->file = $file;
        if ($this->file->isReadable()) {
            $this->load();
        }

        return $this;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->formatConverter->convertString($this->getYaml(), Format::YML, Format::XML);
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
     * @param string $format
     *
     * @return string
     */
    public function getFormattedContents($format)
    {
        switch (strtolower($format)) {
            case Format::YAML:
            case Format::YML:
                return $this->getYaml();
            case Format::XML:
                return $this->getXml();
            default:
                throw new \InvalidArgumentException('Invalid format passed: ' . $format);
        }
    }

    /**
     * @return bool
     */
    protected function load()
    {
        try {
            $parser = $this->getParser($this->getFormat($this->getFile()));
            $definitions = $parser->getDefinitions($this->getFile()->getContents());
            $this->extractParameters($definitions);
            $this->extractServices($definitions);
            $this->extractImports($definitions);

            return true;
        } catch (\Exception $e) {
            throw new IOException(
                sprintf(
                    'Could not parse and extract definitions from file %s.',
                    $this->getFile()
                )
            );
        }
    }

    /**
     * @param SplFileInfo $file
     * @throws \InvalidArgumentException when file is not a supported format.
     *
     * @return string
     */
    protected function getFormat(SplFileInfo $file)
    {
        if (!in_array(strtolower($file->getExtension()), self::getSupportedExtensions())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid format. Expected one of %s, got %s.',
                    implode(',', self::getSupportedExtensions()),
                    $file->getExtension()
                )
            );
        }

        return strtolower($file->getExtension());
    }

    /**
     * @param $format
     * @return ParserInterface
     */
    protected function getParser($format)
    {
        switch (strtolower($format)) {
            case Format::YAML:
            case Format::YML:
                return new YamlParser();
            case Format::XML:
                //I really don't want to deal with XML so let's just deal with Yaml internally.
                //As noted in http://stackoverflow.com/a/6943363/1583093 trying to create a one to one is not
                //ideal.
                return new XmlParser($this->formatConverter, new YamlParser());
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
                $this->addImport($import);
            }
        }
    }
}
