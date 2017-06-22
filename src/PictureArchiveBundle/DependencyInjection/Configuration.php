<?php

namespace PictureArchiveBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 *
 * @package PictureArchiveBundle\DependencyInjection
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pa_app');

        $rootNode
            ->children()
            ->append($this->getFileScannerNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     */
    private function getFileScannerNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('import');
        $node
            ->info('file scanner config')
            ->children()
            ->arrayNode('supported_types')
            ->children()
            ->scalarNode('image')->isRequired()->end()
            ->scalarNode('video')->isRequired()->end()
            ->end()
            ->end()
            ->integerNode('minimum_fileage')->isRequired()->end()
            ->end();

        return $node;
    }
}
