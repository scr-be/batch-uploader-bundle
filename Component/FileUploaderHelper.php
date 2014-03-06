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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Scribe\SharedBundle\Utility\Security;

/**
 * FileUploaderHelper class
 */
class FileUploaderHelper extends FileUploaderConfig
{
	private $session = null;

	public function __construct(ContainerInterface $container = null) 
	{
		parent::__construct($container);

		$this->session = $container->get('session');
	}

	public function getEditId()
	{
		if (!$this->hasEditId()) {
			$this->newEditId();
		}

		return $this->session->get('scribe.digitalhub.editId');
	}

	public function hasEditId()
	{
		if (!$this->session->has('scribe.digitalhub.editId')) {
			return false;
		}

		return true;
	}

	public function newEditId()
	{
		$editId = Security::generateRandom(20, true, '#[^a-z0-9]#i');
		$this->session->set('scribe.digitalhub.editId', $editId);

		return $editId;
	}
}