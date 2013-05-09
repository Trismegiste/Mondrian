<?php

namespace Trismegiste\Mondrian\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Validator implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mondrian');

        $rootNode
                ->children()
                ->arrayNode('graph')->addDefaultsIfNotSet()
                ->children()
                ->arrayNode('calling')
                ->useAttributeAsKey('callee')
                ->prototype('array')
                ->children()
                ->arrayNode('ignore')
                ->prototype('scalar')->end()
                ->end()
                ->end()
                ->end()
                ->end()
                ->end()
                ->end()
                ->end();

        return $treeBuilder;
    }

}