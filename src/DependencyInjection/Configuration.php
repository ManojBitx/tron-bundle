<?php

namespace ManojX\TronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tron');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->arrayNode('http')
            ->children()
            ->scalarNode('host')->end()
            ->scalarNode('api_key')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
