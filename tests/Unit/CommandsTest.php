<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit;

use Dex\Composer\PlugAndPlay\Tests\Application;
use Dex\Composer\PlugAndPlay\Tests\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandsTest extends TestCase
{
    /**
     * @var string
     */
    protected $directory = __DIR__ . '/../Fixtures/Plugin/';

    public function testInstallCommand()
    {
        $application = new Application();
        $input = new StringInput("install --help -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('--plug-and-play', $output->fetch());
    }

    public function testUpdateCommand()
    {
        $application = new Application();
        $input = new StringInput("update --help -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('--plug-and-play', $output->fetch());
    }

    public function testDumpCommand()
    {
        $application = new Application();
        $input = new StringInput("dumpautoload --help -d {$this->directory}");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->assertStringContainsString('--plug-and-play', $output->fetch());
    }
}
