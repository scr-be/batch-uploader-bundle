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

use Scribe\MantleBundle\Component\DependencyInjection\Aware\RequestStackAwareTrait;
use Scribe\Wonka\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Scribe\FileUploaderBundle\Entity\FileUploaderDocument;
use Doctrine\ORM\EntityManager;

/**
 * Class FileUploaderReceiver
 */
class FileUploaderReceiver extends FileUploaderConfig
{
	use RequestStackAwareTrait;

	/**
	 * @var string
	 */
	protected $editId;

	/**
	 * @var bool
	 */
	protected $useEntity;

	/**
	 * @var string|null
	 */
	protected $filesystemPath;

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var array
	 */
	protected $defaultHeaders = [
		'Pragma'                       => 'no-cache',
		'Cache-Control'                => 'no-store, no-cache, must-revalidate',
		'Content-Disposition'          => 'inline; filename="files.json"',
		'X-Content-Type-Options'       => 'nosniff',
		'Access-Control-Allow-Origin'  => '*',
		'Access-Control-Allow-Methods' => 'OPTIONS, HEAD, GET, POST, PUT, DELETE',
		'Access-Control-Allow-Headers' => 'X-File-Name, X-File-Type, X-File-Size',
	];

	/**
	 * @param ContainerInterface|null $container
	 */
	public function __construct(ContainerInterface $container = null)
	{
		parent::__construct($container);

		$this->requestStack = $this->getContainerService('request_stack');
		$this->em = $this->getContainerService('doctrine.orm.entity_manager');
		$this->useEntity 	  = true;
		$this->filesystemPath = null;
	}

	/**
	 * @param $bool
	 *
	 * @return $this
	 */
	public function setUseEntity($bool)
	{
		$this->useEntity = $bool;

		return $this;
	}

	/**
	 * @param $path
	 *
	 * @return $this
	 */
	public function setFilesystemPath($path)
	{
		$this->filesystemPath = $path;

		return $this;
	}

	/**
	 * @param  string $editId
	 *
	 * @return array
	 */
	public function handle($editId = null)
	{
		$this->editId = $editId;

		$method = $this->getMasterRequest()->server->get('REQUEST_METHOD');

		switch ($method) {
			case 'OPTIONS':
				throw new RuntimeException('handleOptions not implemented');
				// @toto: implement list($data, $status, $headers) = $this->handleOptions();
				break;

			case 'HEAD':
			case 'GET':
				list($data, $status, $headers) = $this->handleGet();
				break;

			case 'POST':
				if ($this->getMasterRequest()->request->has('_method') &&
					$this->getMasterRequest()->request->get('_method') === 'DELETE')
				{
					throw new RuntimeException('handleDelete not implemented');
					// @toto: implement list($data, $status, $headers) = $this->handleDelete();
				} else {
					list($data, $status, $headers) = $this->handlePost();
				}
				break;

			case 'DELETE':
				throw new RuntimeException('handleDelete not implemented');
				// @toto: implement list($data, $status, $headers) = $this->handleDelete();
				break;

			default:
				$data    = [];
				$headers = [];
				$status  = [ '405' => 'Method Not Allowed' ];
		}

		$finalHeaders = array_merge($this->defaultHeaders, $headers);

		return [
			$data, 
			$status, 
			$finalHeaders,
		];
	}

	/**
	 * @return array
	 */
	protected function handlePost()
	{
		$files = [];
		$headers = [ 'Vary' => 'Accept' ];
		$status = 200;

		foreach ($this->getMasterRequest()->files as $file) {
			if (!is_array($file)) {
				$files[] = $this->handlePostFile($file);

				continue;
			}

			for ($i = 0; $i < count($file); $i++) {
				$files[] = $this->handlePostFile($file[$i]);
			}
		}

		foreach ($files as $file) {
			if ($file->error !== null) {
				$status = [400 => $file->error];
			}
		}

		return [
			['files' => $files],
			$status,
			$headers
		];
	}

	/**
	 * @param UploadedFile $file
	 *
	 * @return \stdClass
	 */
	protected function handlePostFile(UploadedFile $file)
	{
		return $this->useEntity ? $this->handlePostFileToEntity($file) : $this->handlePostFileToFilesystem($file);
	}

	/**
	 * @param UploadedFile $file
	 *
	 * @return \stdClass
	 */
	protected function handlePostFileToEntity(UploadedFile $file)
	{
		$error = null;

		$document = new FileUploaderDocument();
		$document
			->setName($file->getClientOriginalName())
			->setEditId($this->editId)
			->setSize($file->getSize())
			->setMimeType($file->getMimeType())
			->setExtension(pathinfo($document->getName(), PATHINFO_EXTENSION))
			->setFile(file_get_contents($file->getPathname()));

		$extension = pathinfo($document->getName(), PATHINFO_EXTENSION);
		$guessedExtension = $document->getExtension();

		if (!in_array(strtolower($extension), $this->extensionWhitelist) &&
			!in_array(strtolower($guessedExtension), $this->extensionWhitelist)) {
			$error = 'This filetype ('.$extension.'/'.$guessedExtension.') is not allowed';
		} else {
			$this->em->persist($document);
			$this->em->flush($document);
		}

		return $this->getFileObjectFromEntity($document, $error);
	}

	/**
	 * @param UploadedFile $file
	 *
	 * @return \stdClass
	 */
	protected function handlePostFileToFilesystem(UploadedFile $file)
	{
		$error = null;
		$clientOrigName = $file->getClientOriginalName();

		$file->move($this->filesystemPath, $file->getClientOriginalName());

		return $this->getFileObjectFromFilesystem($this->filesystemPath . DIRECTORY_SEPARATOR . $clientOrigName, $error);
	}

	/**
	 * @return array
	 */
	protected function handleGet()
	{
		return $this->useEntity ? $this->handleGetForEntity() : $this->handleGetForFilesystem();
	}

	/**
	 * @return array
	 */
	protected function handleGetForEntity()
	{
		$filesRepo = $this->em->getRepository('ScribeFileUploaderBundle:FileUploaderDocument');
		$data = [];

		if ($this->getMasterRequest()->request->has('file')) {
			$fileName = basename($this->getMasterRequest()->request->has('get'));
			$file = $filesRepo->findOneByName($fileName);
			$data[] = $this->getFileObjectFromEntity($file);
		} else {
			$files = $filesRepo->findByEditId($this->editId);
			foreach ($files as $file) {
				$data[] = $this->getFileObjectFromEntity($file);
			}
		}

		return [$data, 200, []];
	}

	/**
	 * @return array
	 */
	protected function handleGetForFilesystem()
	{
		return [[], 200, []];
	}

	/**
	 * @param $path
	 * @param null $error
	 *
	 * @return \stdClass
	 */
	protected function getFileObjectFromFilesystem($path, $error = null)
	{
		$router = $this->getContainerService('router');
		$fileObject = new \stdClass();

		if ($error === null) {
			$fileObject->id = md5($path);
		}

		$fileObject->name 	   = basename($path);
		$fileObject->size 	   = filesize($path);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$fileObject->type 	   = finfo_file($finfo, $path);
		$fileObject->extension = pathinfo($path, PATHINFO_EXTENSION);

		if ($error === null) {
			$fileObject->url  	   = $router->generate('scribe_file_uploader_filesystem_url',    ['fileId'   => $fileObject->id]);
			$fileObject->deleteUrl = $router->generate('scribe_file_uploader_filesystem_delete', ['fileName' => urlencode($path)]);
		}

		$fileObject->error      = $error;

		return $fileObject;
	}

	/**
	 * @param FileUploaderDocument $document
	 * @param null $error
	 *
	 * @return \stdClass
	 */
	protected function getFileObjectFromEntity(FileUploaderDocument $document, $error = null)
	{
		$router = $this->getContainerService('router');
		$fileObject = new \stdClass();

		if ($error === null) {
			$fileObject->id = $document->getId();
		}

		$fileObject->name 		= $document->getName();
		$fileObject->size 		= $document->getSize();
		$fileObject->type 		= $document->getMimeType();
		$fileObject->extension  = $document->getExtension();

		if ($error === null) {
			$fileObject->url  		= $router->generate('scribe_file_uploader_file_url', ['fileId' => $document->getId()]);
			$fileObject->deleteUrl = $router->generate('scribe_file_uploader_file_delete', ['fileName' => urlencode($document->getName()), 'fileId' => $document->getId()]);
		}

		$fileObject->error      = $error;

		return $fileObject;
	}
}

/* EOF */
