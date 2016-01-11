<?php

/*
 * This file is part of the Scribe Cache Bundle.
 *
 * (c) Scribe Inc. <source@scribe.software>
 * (c) Matthias Noback <
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Scribe\FileUploaderBundle\Tests;

use Scribe\WonkaBundle\Utility\TestCase\KernelTestCase;

/**
 * Class ScribeFileUploaderBundleTest.
 */
class ScribeFileUploaderBundleTest extends KernelTestCase
{
    public function testCanAccessContainerServices()
    {
        static::assertTrue(static::$staticContainer->has('s.file_uploader.helper_utils'));
        static::assertTrue(static::$staticContainer->has('s.file_uploader.document_receiver'));
        static::assertTrue(static::$staticContainer->has('s.file_uploader.manager.controller'));
        static::assertTrue(static::$staticContainer->has('s.file_uploader.document.repo'));
    }
}

/* EOF */
