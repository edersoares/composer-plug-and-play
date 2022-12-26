<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit;

use Dex\Composer\PlugAndPlay\Tests\Application;
use Dex\Composer\PlugAndPlay\Tests\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ComposerCommandsTest extends TestCase
{
    const WELCOME_MESSAGE = 'You are using Composer Plug and Play Plugin.';

    protected string $directory = __DIR__ . '/../Fixtures/Plugin/';

    public function testPlugAndPlayCommand(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString(self::WELCOME_MESSAGE, $output->fetch());
    }

    public function testPlugAndPlayInstallCommand(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:install --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString(self::WELCOME_MESSAGE, $output->fetch());
    }

    public function testPlugAndPlayUpdateCommand(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:update --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString(self::WELCOME_MESSAGE, $output->fetch());
    }

    public function testPlugAndPlayDumpCommand(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:dump --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString(self::WELCOME_MESSAGE, $output->fetch());
    }

    public function testListCommand(): void
    {
        $application = new Application();
        $input = new StringInput("list -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $content = $output->fetch();

        $this->assertStringContainsString('plug-and-play:dump', $content);
        $this->assertStringContainsString('plug-and-play:install', $content);
        $this->assertStringContainsString('plug-and-play:update', $content);
    }

    public function testPlugAndPlayInitCommand(): void
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:init --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString(self::WELCOME_MESSAGE, $output->fetch());
    }
}
