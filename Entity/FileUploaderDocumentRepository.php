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
}