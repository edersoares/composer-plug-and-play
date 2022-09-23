<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit\Commands;

use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Dex\Composer\PlugAndPlay\Tests\Application;
use Dex\Composer\PlugAndPlay\Tests\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class AddCommandTest extends TestCase
{
    protected $directory = __DIR__ . '/../../tmp';

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
        mkdir($this->directory . '/packages');
        file_put_contents($this->directory . '/composer.json', $composer);
    }

    protected function tearDown(): void
    {
        exec('rm -r ' . $this->directory);
    }

    public function test(): void
    {
        $base = json_encode([
            'minimum-stability' => 'dev',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        file_put_contents($this->directory . PlugAndPlayInterface::PACKAGES_FILE, $base);

        $application = new Application();
        $input = new StringInput("plug-and-play:add -d {$this->directory} dex/extra");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $expected = json_encode([
            'minimum-stability' => 'dev',
            'require' => [
                'dex/extra' => '*',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertFileExists($this->directory . PlugAndPlayInterface::PACKAGES_FILE);
        $this->assertJsonStringEqualsJsonFile($this->directory . PlugAndPlayInterface::PACKAGES_FILE, $expected);
    }

    public function testWithVersion(): void
    {
        $base = json_encode([
            'minimum-stability' => 'dev',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        file_put_contents($this->directory . PlugAndPlayInterface::PACKAGES_FILE, $base);

        $application = new Application();
        $input = new StringInput("plug-and-play:add -d {$this->directory} dex/extra 1.0");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $expected = json_encode([
            'minimum-stability' => 'dev',
            'require' => [
                'dex/extra' => '1.0',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertFileExists($this->directory . PlugAndPlayInterface::PACKAGES_FILE);
        $this->assertJsonStringEqualsJsonFile($this->directory . PlugAndPlayInterface::PACKAGES_FILE, $expected);
    }

    public function testFileNotExists(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:add -d {$this->directory} dex/extra");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $output = $output->fetch();

        $this->assertStringContainsString('The [packages/composer.json] file not exists.', $output);
    }
}
