<?php

/*
 * This file is part of the Scribe Batch Uploader Bundle.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Component;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Scribe\WonkaBundle\Component\DependencyInjection\Container\ContainerAwareTrait;
use Scribe\WonkaBundle\Component\DependencyInjection\Container\ContainerAwareInterface;

/**
 * Class FileUploaderConfig
 */
class FileUploaderConfig implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * @var array
	 */
	protected $options = [];

	/**
	 * @var string
	 */
	protected $baseFilePath;

	/**
	 * @var string
	 */
	protected $baseWebPath;

	/**
	 * @var string[]
	 */
	protected $extensionWhitelist;

	/**
	 * @var bool
	 */
	protected $imageProcessEnableAll;

	/**
	 * @var bool
	 */
	protected $imageProcessThumbnails;
	
	/*
	 * @var string
	 */
	protected $imageProcessThumbnailsPath;

	/**
	 * @var int
	 */
	protected $imageProcessThumbnailsWidth;

	/**
	 * @var int
	 */
	protected $imageProcessThumbnailsHeight;

	/**
	 * @var bool
	 */
	protected $imageProcessThumbnailsSquared;

	/**
	 * @param ContainerInterface|null $container
	 */
	public function __construct(ContainerInterface $container = null) 
	{
		$this->setContainer($container);
		$this->options = $this->getConfigFromContainer();
	}

	/**
	 * @return array
	 */
	public function getConfigFromContainer()
	{
		$this->baseFilePath = $this->getContainerParameter('s.file_uploader.base_filepath');
		$this->baseWebPath = $this->getContainerParameter('s.file_uploader.base_webpath');
		$this->extensionWhitelist = $this->getContainerParameter('s.file_uploader.allowed_extensions');
		$this->imageProcessEnableAll = $this->getContainerParameter('s.file_uploader.image_processing.enable_all');
		$this->imageProcessThumbnails = $this->getContainerParameter('s.file_uploader.image_processing.thumbnails.enabled');
		$this->imageProcessThumbnailsPath = $this->getContainerParameter('s.file_uploader.image_processing.thumbnails.folder');
		$this->imageProcessThumbnailsWidth = $this->getContainerParameter('s.file_uploader.image_processing.thumbnails.width');
		$this->imageProcessThumbnailsHeight = $this->getContainerParameter('s.file_uploader.image_processing.thumbnails.height');
		$this->imageProcessThumbnailsSquared = $this->getContainerParameter('s.file_uploader.image_processing.thumbnails.square');

        $this->processConfig();

		return [
			'file_base_path' 	 => $this->baseFilePath,
			'web_base_path'  	 => $this->baseWebPath,
			'allowed_extensions' => $this->extensionWhitelist
		];
	}

	/**
	 * @return $this
	 */
	protected function processConfig()
	{
		$kernelRootPath = $this
			->getContainerService('kernel')
			->getRootDir();

		$this->baseFilePath = str_replace('%kernel.root_dir%', $kernelRootPath, $this->baseFilePath);

		array_walk($this->extensionWhitelist, function(&$e) {
			$e = strtolower($e);
		});

		return $this;
	}
}

/* EOF */
