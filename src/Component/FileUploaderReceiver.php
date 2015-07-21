<?php
/*
 * This file is part of the Scribe World Application.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Component;

use Symfony\Component\DependencyInjection\ContainerInterface,
	Symfony\Component\HttpFoundation\File\File,
	Symfony\Component\HttpFoundation\File\UploadedFile;
use Scribe\FileUploaderBundle\Entity\FileUploaderDocument;

/**
 * FileUploaderReceiver class
 */
class FileUploaderReceiver extends FileUploaderConfig
{
	/**
	 * @var string
	 */
	private $editId;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var ControllerUtils
	 */
	private $utils;

	/**
	 * @var bool
	 */
	private $useEntity;

	/**
	 * @var string|null
	 */
	private $filesystemPath;

	/**
	 * @var array
	 */
	private $defaultHeaders = [
		'Pragma'                       => 'no-cache',
		'Cache-Control'                => 'no-store, no-cache, must-revalidate',
		'Content-Disposition'          => 'inline; filename="files.json"',
		'X-Content-Type-Options'       => 'nosniff',
		'Access-Control-Allow-Origin'  => '*',
		'Access-Control-Allow-Methods' => 'OPTIONS, HEAD, GET, POST, PUT, DELETE',
		'Access-Control-Allow-Headers' => 'X-File-Name, X-File-Type, X-File-Size',
	];

	public function __construct(ContainerInterface $container = null)
	{
		parent::__construct($container);

		$this->request = $this
			->container
			->get('request_stack')
			->getMasterRequest()
		;

		$this->utils = $this
			->container
			->get('s.utils.controller')
		;

		$this->useEntity 	  = true;
		$this->filesystemPath = null;
	}

	public function setUseEntity($bool)
	{
		$this->useEntity = $bool;

		return $this;
	}

	public function setFilesystemPath($path)
	{
		$this->filesystemPath = $path;

		return $this;
	}

	/**
	 * @param  string $editId
	 * @param  string $projectId
	 * @return array
	 */
	public function handle($editId = null)
	{
		$this->editId = $editId;

		$method = $this->request->server->get('REQUEST_METHOD');

		switch ($method) {
			case 'OPTIONS':
				list($data, $status, $headers) = $this->handleOptions();
				break;

			case 'HEAD':
			case 'GET':
				list($data, $status, $headers) = $this->handleGet();
				break;

			case 'POST':
				if ($this->request->request->has('_method') && $this->request->request->get('_method') === 'DELETE') {
					list($data, $status, $headers) = $this->handleDelete();
				} else {
					list($data, $status, $headers) = $this->handlePost();
				}
				break;

			case 'DELETE':
				list($data, $status, $headers) = $this->handleDelete();
				break;

			default:
				$data    = [];
				$status  = ['405' => 'Method Not Allowed'];
				$headers = [];
		}

		$finalHeaders = array_merge($this->defaultHeaders, $headers);

		return [
			$data, 
			$status, 
			$finalHeaders,
		];
	}

	private function handlePost()
	{
		$files = [];
		$headers = [
			'Vary' => 'Accept'
		];
		$status = 200;

		foreach ($this->request->files as $file) {

			if (is_array($file)) {
				for ($i = 0; $i < count($file); $i++) {
					$files[] = $this->handlePostFile($file[$i]);
				}
			} else {
				$files[] = $this->handlePostFile($file);
			}
			
		}

		foreach ($files as $file) {
			if ($file->error !== null) {
				$status = [400 => $file->error];
			}
		}

		return [['files' => $files], $status, $headers];
	}

	private function handlePostFile(UploadedFile $file)
	{
		if ($this->useEntity === true) {
			return $this->handlePostFileToEntity($file);
		} else {
			return $this->handlePostFileToFilesystem($file);
		}
	}

	private function handlePostFileToEntity(UploadedFile $file)
	{
		$error = null;

		$document = new FileUploaderDocument();
		$document
			->setName($file->getClientOriginalName())
			->setEditId($this->editId)
			->setSize($file->getSize())
			->setMimeType($file->getMimeType())
			->setExtension(pathinfo($document->getName(), PATHINFO_EXTENSION))
			->setFile(file_get_contents($file->getPathname()))
		;

		$extension        = pathinfo($document->getName(), PATHINFO_EXTENSION);
		$guessedExtension = $document->getExtension();

		if (!in_array(strtolower($extension), $this->allowed_extensions) && !in_array(strtolower($guessedExtension), $this->allowed_extensions)) {
			$error = 'This filetype ('.$extension.'/'.$guessedExtension.') is not allowed';
		} else {
			$this->utils->entityPersist($document);
		}

		return $this->getFileObjectFromEntity($document, $error);
	}

	private function handlePostFileToFilesystem(UploadedFile $file)
	{
		$error = null;

		$clientOrigName = $file->getClientOriginalName();

		$extension        = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
		$guessedExtension = $file->guessExtension();

		$file->move($this->filesystemPath, $file->getClientOriginalName());

		return $this->getFileObjectFromFilesystem($this->filesystemPath . DIRECTORY_SEPARATOR . $clientOrigName, $error);
	}

	private function handleGet()
	{
		$data = [];

		if ($this->useEntity === true) {
			return $this->handleGetForEntity();
		} else {
			return $this->handleGetForFilesystem();
		}
	}

	private function handleGetForEntity()
	{
		$filesRepo = $this
			->container
			->get('doctrine.orm.default_entity_manager')
			->getRepository('ScribeFileUploaderBundle:FileUploaderDocument')
		;

		$data = [];
		if ($this->request->request->has('file')) {
			$fileName = basename($this->request->request->has('get'));
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

	private function handleGetForFilesystem()
	{
		if ($this->request->request->has('file')) {
			$fileName = basename($this->request->request->has('get'));
			$file = $this->filesystemPath . DIRECTORY_SEPARATOR . $fileName;
			$data[] = $this->getFileObjectFromEntity($file);
		} else {
			$files = $filesRepo->findByEditId($this->editId);
			foreach ($files as $file) {
				$data[] = $this->getFileObjectFromEntity($file);
			}
		}

		return [$data, 200, []];
	}

	private function getFileObjectFromFilesystem($path, $error = null)
	{
		$router = $this
			->utils
			->getService('router')
		;

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

	private function getFileObjectFromEntity(FileUploaderDocument $document, $error = null)
	{
		$router = $this
			->utils
			->getService('router')
		;

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
