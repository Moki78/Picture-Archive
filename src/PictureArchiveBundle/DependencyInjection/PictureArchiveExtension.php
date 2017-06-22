<?php

namespace PictureArchiveBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PictureArchiveExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $config['import'] = array_merge(
            $config['import'],
            array(
                'base_directory' => $container->getParameter('picture_archive.base_directory'),
                'type_path' => $container->getParameter('picture_archive.type_path'),
                'failed_directory' => $container->getParameter('picture_archive.import_failed_directory')
            )
        );
        $container->setParameter("{$this->getAlias()}.import", $config['import']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
