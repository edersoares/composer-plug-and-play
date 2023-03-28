<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\IO\BufferIO;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;

abstract class UseCase extends TestCase
{
    protected string $cwd;
    protected Factory $factory;
    protected BufferIO $io;

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
        $this->factory = new Factory();
        $this->io = new BufferIO();

        $this->factory->createComposer(io: $this->io, cwd: $this->path());

        return $this->factory;
    }

    protected function assertGeneratedJsonEquals(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $this->assertStringEqualsFile($this->getPackageFilename(), $json);
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString($output, $this->io->getOutput());
    }
}