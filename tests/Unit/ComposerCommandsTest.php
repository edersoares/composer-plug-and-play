<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit;

use Dex\Composer\PlugAndPlay\Tests\Application;
use Dex\Composer\PlugAndPlay\Tests\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ComposerCommandsTest extends TestCase
{
    /**
     * @var string
     */
    protected $directory = __DIR__ . '/../Fixtures/Plugin/';

    public function testPlugAndPlayCommand()
    {
        $application = new Application();
        $input = new StringInput("plug-and-play --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }

    public function testPlugAndPlayInstallCommand()
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:install --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }

    public function testPlugAndPlayUpdateCommand()
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:update --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }

    public function testPlugAndPlayDumpCommand()
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:dump --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }

    public function testListCommand()
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

    public function testPlugAndPlayInitCommand()
    {
        $application = new Application();
        $input = new StringInput("plug-and-play:init --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }
}
