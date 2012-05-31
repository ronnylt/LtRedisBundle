<?php

namespace Lt\Bundle\RedisBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lt_redis');

        $this->addConnectionsSection($rootNode);
        $this->addSessionStorageHanderSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds connections configuration
     *
     * @param ArrayNodeDefinition $rootNode
     */
    private function addConnectionsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('connection')
            ->children()
                ->arrayNode('connections')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('alias', false)
                    ->prototype('array')
                        ->fixXmlConfig('dsn')
                        ->children()
                            ->scalarNode('alias')->isRequired()->end()
                            ->booleanNode('logging')->defaultValue('%kernel.debug%')->end()
                            ->scalarNode('host')->defaultValue('localhost')->end()
                            ->scalarNode('port')->defaultValue(6379)->end()
                            ->scalarNode('database')->defaultValue(0)->end()
                            ->scalarNode('auth')->defaultValue('')->end()
                            ->arrayNode('options')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('connection_async')->defaultFalse()->end()
                                    ->booleanNode('connection_persistent')->defaultFalse()->end()
                                    ->scalarNode('connection_timeout')->defaultValue(5)->end()
                                    ->scalarNode('read_write_timeout')->defaultNull()->end()
                                    ->booleanNode('iterable_multibulk')->defaultFalse()->end()
                                    ->booleanNode('throw_errors')->defaultTrue()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds Session Storage Hander configuration
     *
     * @param ArrayNodeDefinition $rootNode
     */
    private function addSessionStorageHanderSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('session')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('connection')->isRequired()->end()
                        ->scalarNode('prefix')->defaultValue('session')->end()
                    ->end()
                ->end()
            ->end();
    }
}
