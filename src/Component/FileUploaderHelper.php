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

use Scribe\WonkaBundle\Utility\Security\Security;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class FileUploaderHelper
 */
class FileUploaderHelper extends FileUploaderConfig
{
	/**
	 * @var SessionInterface
	 */
	protected $session = null;

	/**
	 * @param SessionInterface   $session
	 * @param ContainerInterface $container
	 */
	public function __construct(SessionInterface $session, ContainerInterface $container)
	{
		$this->session = $session;

		parent::__construct($container);
	}

	/**
	 * @return mixed
	 */
	public function getEditId()
	{
		if (!$this->hasEditId()) {
			$this->newEditId();
		}

		return $this->session->get('scribe.digitalhub.editId');
	}

	/**
	 * @return bool
	 */
	public function hasEditId()
	{
		if (!$this->session->has('scribe.digitalhub.editId')) {
			return false;
		}

		return true;
	}

	/**
	 * @return string
	 */
	public function newEditId()
	{
		$id = Security::getRandomHash();
		$this->session->set('scribe.digitalhub.editId', $id);

		return $id;
	}
}

/* EOF */
