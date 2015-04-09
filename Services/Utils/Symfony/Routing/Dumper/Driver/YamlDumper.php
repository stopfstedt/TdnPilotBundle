<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony\Routing\Dumper\Driver;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Yaml\Yaml;

class YamlDumper implements DumperInterface
{
    /**
     * @param RouteCollection $routes
     * @return string
     */
    public function dump(RouteCollection $routes)
    {
        $definitions = [];

        foreach ($routes->all() as $name => $route) {
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
