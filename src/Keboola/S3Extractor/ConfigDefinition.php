<?php

namespace Keboola\S3Extractor;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigDefinition implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('parameters');

        $rootNode
            ->children()
                ->scalarNode('accessKeyId')->end()
                ->scalarNode('#secretAccessKey')->end()
                ->scalarNode('region')->end()
                ->scalarNode('bucket')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('key')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
