<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\IO\BufferIO;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;

abstract class UseCase extends TestCase
{
    protected string $cwd;
    protected string $output;

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
        $factory = new Factory();
        $io = new BufferIO();

        $factory->createComposer(io: $io, cwd: $this->path());

        $this->output = $io->getOutput();

        return $factory;
    }

    protected function assertGeneratedJsonEquals(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $this->assertStringEqualsFile($this->getPackageFilename(), $json);
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString($output, $this->output);
    }
}
