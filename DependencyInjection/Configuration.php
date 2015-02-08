<?php

namespace Tdn\PilotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Tdn\PilotBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('tdn_pilot');

        $rootNode
            ->children()
                ->arrayNode('output')
                    ->children()
                        ->scalarNode('engine')->defaultValue('twig_output_engine')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
