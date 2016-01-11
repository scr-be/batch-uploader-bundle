<?php

/*
 * This file is part of the Scribe Batch Uploader Bundle.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Scribe\FileUploaderBundle\Entity\FileUploaderDocumentRepository;

/**
 * Class ManagerController
 */
class ManagerController
{
	/**
     * @var FileUploaderDocumentRepository
     */
    private $fileUploaderDocumentRepo;

    /**
     * @param FileUploaderDocumentRepository $fileUploaderDocumentRepo
     */
    public function __construct(FileUploaderDocumentRepository $fileUploaderDocumentRepo)
    {
        $this->fileUploaderDocumentRepo = $fileUploaderDocumentRepo;
    }

    public function getFileAction($fileId)
    {
        try {
            $object = $this
                ->fileUploaderDocumentRepo
                ->findOneById($fileId);

        } catch (\Exception $e) {
            throw new NotFoundHttpException('The requested file could not be found');
        }

        return Response::create(stream_get_contents($object->getFile()), 200, [
            'Content-Type'              => 'application/octet-stream',
            'Content-Disposition'       => 'attachment; filename="' . $object->getName() . '"',
            'Content-Length'            => $object->getSize(),
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate',
            'Pragma'                    => 'public',
        ]);
    }
}

/* EOF */
