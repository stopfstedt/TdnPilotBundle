<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper;

use Tuck\ConverterBundle\Exception\UnknownFormatException;
use Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\DumperInterface;

class StandardDumperFactory implements DumperFactoryInterface
{
    protected $dumperMap = [
        'xml'  => '\Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\XmlDumper',
        'yaml' => '\Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\YamlDumper',
        'yml'  => '\Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver\YamlDumper'
    ];

    /**
     * @param $type
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    public function getDumper($type)
    {
        $class = $this->getClassFromType($type);

        return new $class();
    }

    /**
     * @param string $type
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    protected function getClassFromType($type)
    {
        if (!isset($this->dumperMap[$type])) {
            throw UnknownFormatException::create($type);
        }

        return $this->dumperMap[$type];
    }
}
