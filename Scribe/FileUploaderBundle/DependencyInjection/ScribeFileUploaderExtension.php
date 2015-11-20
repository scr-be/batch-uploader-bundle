<?php
/*
 * This file is part of the Scribe World Application.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader,
    Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * ScribeFileUploaderExtension class
 */
class ScribeFileUploaderExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration(
            $configuration, 
            $configs
        );

        $container->setParameter(
            'scribe_file_uploader.base_filepath',
            $config['base_filepath']
        );

        $container->setParameter(
            'scribe_file_uploader.base_webpath',
            $config['base_webpath']
        );

        $container->setParameter(
            'scribe_file_uploader.filecount_max',
            $config['filecount_max']
        );

        $container->setParameter(
            'scribe_file_uploader.filesize_max',
            $config['filesize_max']
        );

        $container->setParameter(
            'scribe_file_uploader.filesize_type',
            $config['filesize_type']
        );

        $container->setParameter(
            'scribe_file_uploader.allowed_extensions',
            $config['allowed_extensions']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.enable_all',
            $config['image_processing']['enable_all']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.thumbnails.enabled',
            $config['image_processing']['thumbnails']['enabled']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.thumbnails.folder',
            $config['image_processing']['thumbnails']['folder']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.thumbnails.width',
            $config['image_processing']['thumbnails']['width']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.thumbnails.height',
            $config['image_processing']['thumbnails']['height']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.thumbnails.square',
            $config['image_processing']['thumbnails']['square']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.small.enabled',
            $config['image_processing']['small']['enabled']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.small.folder',
            $config['image_processing']['small']['folder']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.small.width',
            $config['image_processing']['small']['width']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.small.height',
            $config['image_processing']['small']['height']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.small.square',
            $config['image_processing']['small']['square']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.medium.enabled',
            $config['image_processing']['medium']['enabled']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.medium.folder',
            $config['image_processing']['medium']['folder']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.medium.width',
            $config['image_processing']['medium']['width']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.medium.height',
            $config['image_processing']['medium']['height']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.medium.square',
            $config['image_processing']['medium']['square']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.large.enabled',
            $config['image_processing']['large']['enabled']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.large.folder',
            $config['image_processing']['large']['folder']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.large.width',
            $config['image_processing']['large']['width']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.large.height',
            $config['image_processing']['large']['height']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.large.square',
            $config['image_processing']['large']['square']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.extreme.enabled',
            $config['image_processing']['extreme']['enabled']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.extreme.folder',
            $config['image_processing']['extreme']['folder']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.extreme.width',
            $config['image_processing']['extreme']['width']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.extreme.height',
            $config['image_processing']['extreme']['height']
        );

        $container->setParameter(
            'scribe_file_uploader.image_processing.extreme.square',
            $config['image_processing']['extreme']['square']
        );

        $loader = new YamlFileLoader(
            $container, 
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
    }
}
