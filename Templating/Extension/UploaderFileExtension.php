<?php
/*
 * This file is part of the Scribe World Application.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Templating\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Twig_Extension,
    Twig_Function_Method;

/**
 * UploaderFileExtension class
 */
class UploaderFileExtension extends Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param  ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    /**
     * @param  ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'scribe_file_uploader_get_files' => new Twig_Function_Method($this, 'getFiles'),
        ];
    }

    /**
     * @param  string $folder
     * @return array
     */
    public function getFiles($folder)
    {
        return $this
            ->container
            ->get('scribe.file_uploader')
            ->getFiles(array('folder' => $folder))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'scribe_file_uploader';
    }
}
