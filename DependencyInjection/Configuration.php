<?php

namespace Tdn\PilotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Tdn\PilotBundle\FormatConverter
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
                ->arrayNode('template')
                    ->addDefaultsIfNotSet()
                    ->append($this->getStrategy())
                ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @return ScalarNodeDefinition
     */
    protected function getStrategy()
    {
        $node = new ScalarNodeDefinition('strategy');
        $node->defaultValue('twig_template_strategy');

        return $node;
    }
}
