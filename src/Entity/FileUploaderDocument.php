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

use Scribe\Doctrine\ORM\Mapping\IdEntity;
use Scribe\MantleBundle\Doctrine\Base\Model\Name\HasName;

/**
 * Class FileUploaderDocument
 */
class FileUploaderDocument extends IdEntity
{
    use HasName;

    /**
     * @var string
     */
    protected $editId;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $file;

    /**
     * @param  string $editId
     *
     * @return $this
     */
    public function setEditId($editId)
    {
        $this->editId = $editId;
    
        return $this;
    }

    /**
     * @return string 
     */
    public function getEditId()
    {
        return $this->editId;
    }

    /**
     * @param  string $mimeType
     *
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    
        return $this;
    }

    /**
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param  string $extension
     *
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    
        return $this;
    }

    /**
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param  string $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
    
        return $this;
    }

    /**
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param  string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
    
        return $this;
    }

    /**
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }
}

/* EOF */
