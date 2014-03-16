<?php

namespace Scribe\FileUploaderBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FileUploaderDocumentRepository
 */
class FileUploaderDocumentRepository extends EntityRepository
{
	/**
	 * @return integer
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
	 * @return integer
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