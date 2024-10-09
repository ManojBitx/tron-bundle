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
            ->scalarNode('default_network')
            ->defaultValue('mainnet')
            ->end()
            ->arrayNode('networks')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
            ->arrayNode('fullNode')
            ->children()
            ->scalarNode('host')->isRequired()->end()
            ->scalarNode('api_key')->defaultNull()->end()
            ->end()
            ->end()
            ->arrayNode('solidityNode')
            ->children()
            ->scalarNode('host')->end()
            ->scalarNode('api_key')->defaultNull()->end()
            ->end()
            ->end()
            ->arrayNode('explorer')
            ->children()
            ->scalarNode('host')->isRequired()->end()
            ->scalarNode('api_key')->defaultNull()->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
