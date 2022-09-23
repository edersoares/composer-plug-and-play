<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit\Commands;

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
        file_put_contents($this->directory . '/composer.json', $composer);
    }

    protected function tearDown(): void
    {
        exec('rm -r ' . $this->directory);
    }

    public function test(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:add -d {$this->directory} dex/extra");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $expected = json_encode([
            'name' => 'dex/test',
            'config' => [
                'allow-plugins' => [
                    'dex/composer-plug-and-play' => true,
                    'dex/fake' => true,
                ],
            ],
            'require' => [
                'dex/extra' => '*',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertJsonStringEqualsJsonFile($expected, $this->directory . '/packages/composer.json');
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
