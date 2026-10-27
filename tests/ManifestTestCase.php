<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Dex\Composer\PlugAndPlay\Manifest;

abstract class ManifestTestCase extends TestCase
{
    use TestConcerns;

    protected function manifest(): void
    {
        Manifest::write();
    }
}
