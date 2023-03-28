<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;

trait TestConcerns
{
    protected string $cwd;
    protected string $output;

    abstract protected function fixture(): string;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cwd = getcwd();
        $from = $this->fixturesPath();
        $tmp = $this->path() . $this->fixture();

        exec("cp -R $from $tmp");

        chdir($tmp);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $tmp = $this->path() . $this->fixture();

        exec("rm -r $tmp");

        chdir($this->cwd);
    }

    protected function path(): string
    {
        return __DIR__ . '/../tmp/';
    }

    protected function fixturesPath(): string
    {
        return __DIR__ . '/../fixtures/' . $this->fixture();
    }

    protected function assertGeneratedJsonEquals(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $path = $this->path() . $this->fixture() . '/' . PlugAndPlayInterface::FILENAME;

        $this->assertStringEqualsFile($path, $json);
    }

    protected function assertPackagesFileJsonEquals(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $path = $this->path() . $this->fixture() . '/' . PlugAndPlayInterface::PACKAGES_FILE;

        $this->assertStringEqualsFile($path, $json);
    }

    protected function assertOutputContains(string $string): void
    {
        $this->assertStringContainsString($string, $this->output);
    }
}
