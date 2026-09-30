<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\IO\BufferIO;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use Dex\Composer\PlugAndPlay\Manifest;

abstract class ManifestTestCase extends TestCase
{
    use TestConcerns;

    protected function manifest(): void
    {
        Factory::restart();

        $factory = new Factory();

        $composer = $factory->createComposer(io: new BufferIO(), cwd: $this->path() . $this->fixture);

        Manifest::write($composer);
    }
}
