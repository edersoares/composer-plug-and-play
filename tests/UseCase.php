<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\IO\IOInterface;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;

abstract class UseCase extends TestCase
{
    protected string $cwd;
    protected Factory $factory;

    abstract protected function path(): string;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cwd = getcwd();

        chdir($this->path());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Factory::restart();

        if (is_file($this->getPackageFilename())) {
            unlink($this->getPackageFilename());
        }

        chdir($this->cwd);
    }

    protected function getPackageFilename(): string
    {
        return $this->path() . PlugAndPlayInterface::FILENAME;
    }

    protected function factory(): Factory
    {
        $io = $this->createMock(IOInterface::class);

        $factory = new Factory();

        $factory->createComposer(io: $io, cwd: $this->path());

        return $factory;
    }

    protected function assertGeneratedJsonEquals(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $this->assertStringEqualsFile($this->getPackageFilename(), $json);
    }
}
