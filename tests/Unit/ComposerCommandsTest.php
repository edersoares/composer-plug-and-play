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

    public function testInstallCommand()
    {
        $application = new Application();
        $input = new StringInput("install --plug-and-play --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }

    public function testUpdateCommand()
    {
        $application = new Application();
        $input = new StringInput("update --plug-and-play --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }

    public function testDumpautoloadCommand()
    {
        $application = new Application();
        $input = new StringInput("dumpautoload --plug-and-play --plug-and-play-pretend -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('You are using Composer Plug and Play Plugin.', $output->fetch());
    }

    public function testIfOptionIsPresentInInstallCommand()
    {
        $application = new Application();
        $input = new StringInput("install --help -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('--plug-and-play', $output->fetch());
    }

    public function testIfOptionIsPresentInUpdateCommand()
    {
        $application = new Application();
        $input = new StringInput("update --help -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('--plug-and-play', $output->fetch());
    }

    public function testIfOptionIsPresentInDumpautoloadCommand()
    {
        $application = new Application();
        $input = new StringInput("dumpautoload --help -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('--plug-and-play', $output->fetch());
    }
}
