<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit\Commands;

use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Dex\Composer\PlugAndPlay\Tests\OldCommandTestCase;

class AddOldCommandTest extends OldCommandTestCase
{
    protected function setUp(): void
    {
        $composer = json_encode([
            'name' => 'dex/test',
            'config' => [
                'allow-plugins' => [
                    'dex/composer-plug-and-play' => true,
                    'dex/fake' => true,
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        mkdir($this->directory);
        mkdir($this->directory . PlugAndPlayInterface::PACKAGES_PATH);
        file_put_contents($this->directory . 'composer.json', $composer);
    }

    protected function createPackagesComposerJson(): void
    {
        $base = json_encode([
            'minimum-stability' => 'dev',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        file_put_contents($this->packagesFile(), $base);
    }

    protected function tearDown(): void
    {
        exec('rm -r ' . $this->directory);
    }

    public function test(): void
    {
        $this->createPackagesComposerJson();

        $this->runCommand('plug-and-play:add dex/extra');

        $expected = $this->encodeContent([
            'minimum-stability' => 'dev',
            'require' => [
                'dex/extra' => '*',
            ],
        ]);

        $this->assertFileExists($this->packagesFile());
        $this->assertJsonStringEqualsJsonFile($this->packagesFile(), $expected);
    }

    public function testWithVersion(): void
    {
        $this->createPackagesComposerJson();

        $this->runCommand('plug-and-play:add dex/extra 1.0');

        $expected = $this->encodeContent([
            'minimum-stability' => 'dev',
            'require' => [
                'dex/extra' => '1.0',
            ],
        ]);

        $this->assertFileExists($this->packagesFile());
        $this->assertJsonStringEqualsJsonFile($this->packagesFile(), $expected);
    }

    public function testMergeJsonFiles(): void
    {
        $this->runCommand('plug-and-play:add dex/extra');

        $expected = $this->encodeContent([
            'require' => [
                'dex/extra' => '*',
            ],
        ]);

        $this->assertJsonStringEqualsJsonFile($this->packagesFile(), $expected);
    }
}
