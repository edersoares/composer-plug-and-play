<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit\Commands;

use Dex\Composer\PlugAndPlay\Tests\Application;
use Dex\Composer\PlugAndPlay\Tests\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class PlugAndPlayCommandTest extends TestCase
{
    protected string $directory = __DIR__ . '/../../tmp';

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

    public function testPackagesDirectoryAndFilesAreCreated(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output); // Runs UpdateCommand
        $application->doRun($input, $output); // Runs InstallCommand

        $this->assertDirectoryExists($this->directory . '/packages');
        $this->assertFileExists($this->directory . '/packages/plug-and-play.json');
        $this->assertFileExists($this->directory . '/packages/plug-and-play.lock');
    }
}
