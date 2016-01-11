<?php

/*
 * This file is part of the Scribe Batch Uploader Bundle.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FileUploaderDocumentRepository
 */
class FileUploaderDocumentRepository extends EntityRepository
{
	/**
	 * @param int $id
	 *
	 * @return mixed|null
	 */
	public function findOneById($id)
	{
		$q = $this
			->createQueryBuilder('d')
			->where('d.id = :id')
			->setParameter('id', $id)
			->getQuery();

		try {
			$r = $q->getSingleResult();
		} catch (\Exception $e) {
			return null;
		}

		return $r;
	}

	/**
	 * @param int $editId
	 *
	 * @return mixed
	 */
	public function findCountByEditId($editId)
	{
		$q = $this
			->createQueryBuilder('f')
    		->select('count(f.id)')
    		->where('f.editId = :editId')
			->setParameter('editId', $editId)
    		->getQuery()
    	;

    	return $q->getSingleScalarResult();
	}

	/**
	 * @param int $editId
	 * @param array $exts
	 *
	 * @return array
	 */
	public function findImagesByEditId($editId, $exts = ['jpeg', 'jpg', 'png', 'bmp', 'tiff', 'tif'])
	{
		$qb = $this
			->createQueryBuilder('f')
		;

		$whereOrX = [];
		for ($i = 0; $i < count($exts); $i++) {
			$whereOrX[] = $qb->expr()->eq('f.extension', ':ext'.$i);
		}
		$exp = $qb->expr();
		$orx = call_user_func_array([$exp, 'orX'], $whereOrX);

		$qb
    		->where('f.editId = :editId')
    		->andWhere($orx)
			->setParameter('editId', $editId)
		;

		for ($i = 0; $i < count($exts); $i++) {
			$qb->setParameter('ext'.$i, $exts[$i]);
		}

		$q = $qb
    		->getQuery()
    	;

    	return $q->getResult();
	}
}

/* EOF */
