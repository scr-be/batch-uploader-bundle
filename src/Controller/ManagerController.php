<?php
/*
 * This file is part of the Scribe World Application.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Scribe\MantleBundle\Component\Controller\Behaviors\ControllerBehaviors;
use Scribe\FileUploaderBundle\Entity\FileUploaderDocumentRepository;

/**
 * Class ManagerController.
 */
class ManagerController extends ControllerBehaviors
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

    /**
     * @param string $fileId
     *
     * @return mixed
     */
    public function getFileAction($fileId)
    {
        try {
            $document = $this
                ->fileUploaderDocumentRepo
                ->findOneById($fileId)
            ;
        } catch (\Exception $e) {
            throw new NotFoundHttpException('The requested file could not be found');
        }

        return $this->getResponse(
            stream_get_contents($document->getFile()),
            [
                'Content-Type'              => 'application/octet-stream',
                'Content-Disposition'       => 'attachment; filename="' . $document->getName() . '"',
                'Content-Length'            => $document->getSize(),
                'Content-Transfer-Encoding' => 'binary',
                'Expires'                   => 0,
                'Cache-Control'             => 'must-revalidate',
                'Pragma'                    => 'public',
            ],
            200);
    }
}