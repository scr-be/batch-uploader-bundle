<?php

/*
 * This file is part of the Scribe File Uploader Bundle.
 *
 * (c) Scribe Inc. <https://scr.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Uploader\ConfigManager;

use Scribe\MantleBundle\Component\Controller\Behaviors\ControllerBehaviorsTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FileUploaderConfig.
 */
class FileUploaderConfig
{
    use ControllerBehaviorsTrait;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);
        $this->options = $this->getConfigFromContainer();
    }

    public function getConfigFromContainer()
    {
        $this->base_filepath = $this->container->getParameter('scribe_file_uploader.base_filepath');
        $this->base_webpath = $this->container->getParameter('scribe_file_uploader.base_webpath');
        $this->allowed_extensions = $this->container->getParameter('scribe_file_uploader.allowed_extensions');
        $this->image_processing_enable_all = $this->container->getParameter('scribe_file_uploader.image_processing.enable_all');
        $this->image_processing_thumbnails_enabled = $this->container->getParameter('scribe_file_uploader.image_processing.thumbnails.enabled');
        $this->image_processing_thumbnails_folder = $this->container->getParameter('scribe_file_uploader.image_processing.thumbnails.folder');
        $this->image_processing_thumbnails_width = $this->container->getParameter('scribe_file_uploader.image_processing.thumbnails.width');
        $this->image_processing_thumbnails_height = $this->container->getParameter('scribe_file_uploader.image_processing.thumbnails.height');
        $this->image_processing_thumbnails_square = $this->container->getParameter('scribe_file_uploader.image_processing.thumbnails.square');

        $kernel_root_dir = $this->container->get('kernel')->getRootDir();
        $this->base_filepath = str_replace('%kernel.root_dir%', $kernel_root_dir, $this->base_filepath);

        foreach ($this->allowed_extensions as &$ext) {
            $ext = strtolower($ext);
        }

        return [
            'file_base_path' => $this->base_filepath,
            'web_base_path' => $this->base_webpath,
            'allowed_extensions' => $this->allowed_extensions,
        ];
    }
}
