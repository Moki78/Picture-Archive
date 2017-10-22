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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('picture_archive');

        $rootNode
            ->children()
            ->append($this->getArchiveNode())
            ->append($this->getImportNode())
            ->append($this->getToolsNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     * @throws \RuntimeException
     */
    private function getArchiveNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('archive');
        $node
            ->info('archive configuration')
            ->children()
                ->scalarNode('base_directory')->isRequired()->defaultNull()->end()
            ->arrayNode('supported_types')
                    ->children()
            ->arrayNode('image')->prototype('scalar')->end()->end()
            ->arrayNode('video')->prototype('scalar')->end()->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * @return NodeDefinition
     * @throws \RuntimeException
     */
    private function getImportNode(): NodeDefinition
    {
        $builder = new TreeBuilder();
        $node = $builder->root('import');
        $node
            ->info('import configuration')
            ->children()
                ->scalarNode('base_directory')->isRequired()->defaultNull()->end()
                ->scalarNode('failed_directory')->isRequired()->defaultNull()->end()
                ->integerNode('minimum_fileage')->isRequired()->end()
            ->end();

        return $node;
    }

    /**
     * @return NodeDefinition
     * @throws \RuntimeException
     */
    private function getToolsNode(): NodeDefinition
    {
        $builder = new TreeBuilder();
        $node = $builder->root('tools');
        $node
            ->info('tool configuration')
            ->children()
                ->scalarNode('image_info')->isRequired()->defaultNull()->end()
            ->end();

        return $node;
    }
}
