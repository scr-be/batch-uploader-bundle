# Contributing

Ensure the following requirements are met when contributing pull-requests to this project:

- Files have been run through `php-cs-fixer` with the following argument `--level=symfony`
- All files contain a file-level doc-block using the following template
  ```php
  /*
   * This file is part of the [Name of Library/Bundle].
   *
   * (c) Scribe Inc. <open@scr.be>
   *
   * For the full copyright and license information, please view the LICENSE.md
   * file that was distributed with this source code.
   */
  ```
- Any desired contributor attribution is added at the class or method level using the `@author` PHPDoc syntax. For example
  ```php
  /**
   * [NameOfClass] Class.
   *
   * @author [Contributor Name] <[contributor@email]>
   */
  class NameOfClass
  { // ...
  ```
