<?php

namespace eap1985\NewsTopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('news_top');

        $treeBuilder->getRootNode()
            ->children()
            ->booleanNode('enable_soft_delete')
            ->isRequired()
            ->end()
            ->end();

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('locales')
            ->addDefaultChildrenIfNoneSet()
            ->arrayPrototype()
            ->children()
            ->scalarNode('code')
            ->defaultValue('ru')
            ->end()
            ->scalarNode('label')
            ->defaultValue('Русский')
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}