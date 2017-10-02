<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 19.04.2016
 * Time: 11:14.
 */

namespace Turted\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('turted');

        $rootNode
                ->children()
                    ->booleanNode('enabled')
                        ->defaultFalse()
                    ->end()
                    ->arrayNode('push')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('url')
                                ->defaultValue('http://127.0.0.1:7117/push/')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('password')
                                ->defaultValue('turtedpasswd_to_change')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()// push
                ->end();

        return $treeBuilder;
    }
}
