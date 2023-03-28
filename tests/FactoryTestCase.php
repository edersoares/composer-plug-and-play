<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\IO\BufferIO;
use Dex\Composer\PlugAndPlay\Composer\Factory;

abstract class FactoryTestCase extends TestCase
{
    use TestConcerns;

    protected function factory(): Factory
    {
        $factory = new Factory();
        $io = new BufferIO();

        $factory->createComposer(io: $io, cwd: $this->path() . $this->fixture());

        $this->output = $io->getOutput();

        return $factory;
    }
}
