<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\IO\BufferIO;
use Dex\Composer\PlugAndPlay\Composer\Factory;

abstract class FactoryTestCase extends TestCase
{
    use TestConcerns;

    protected function factory(): void
    {
        Factory::restart();

        $factory = new Factory();
        $io = new BufferIO();

        $factory->createComposer(io: $io, cwd: $this->path() . $this->fixture());

        $this->output = $io->getOutput();
    }
}
