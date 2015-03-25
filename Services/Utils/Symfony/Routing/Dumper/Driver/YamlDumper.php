<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlDumper
 * @package Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver
 */
class YamlDumper extends AbstractDumper implements DumperInterface
{
    /**
     * Dumps the YAML representation of a Route Collection.
     *
     * @return string
     */
    public function dump()
    {
        $definitions = [];

        foreach ($this->routeCollection->all() as $name => $route) {
            $definitions[$name] = [
                'path' => $route->getPath(),
                'defaults' => $route->getDefaults()
            ];

            if (count($route->getRequirements())) {
                $definitions[$name]['requirements'] = $route->getRequirements();
            }

            if (count($route->getOptions())) {
                $definitions[$name]['options'] = $route->getOptions();
            }

            if ($route->getHost()) {
                $definitions[$name]['host'] = $route->getHost();
            }

            if (count($route->getSchemes())) {
                $definitions[$name]['schemes'] = $route->getSchemes();
            }

            if (count($route->getMethods())) {
                $definitions[$name]['methods'] = $route->getMethods();
            }

            if ($route->getCondition()) {
                $definitions[$name]['condition'] = $route->getCondition();
            }
        }

        return Yaml::dump($definitions);
    }
}
