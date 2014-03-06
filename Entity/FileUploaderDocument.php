<?php
/*
 * This file is part of the Scribe World Application.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
    
/**
 * ConverterStep
 */
class FileUploaderDocument
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $editId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var int
     */
    private $size;

    /**
     * @var blob
     */
    private $file;

    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $editId
     * @return ConverterStep
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
     * @param  string $name
     * @return ConverterStep
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $mimeType
     * @return ConverterStep
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
     * @return ConverterStep
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
     * @return ConverterStep
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
     * @return ConverterStep
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
