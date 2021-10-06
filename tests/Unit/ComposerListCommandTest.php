<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit;

use Dex\Composer\PlugAndPlay\Tests\Application;
use Dex\Composer\PlugAndPlay\Tests\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ComposerListCommandTest extends TestCase
{
    /**
     * @var string
     */
    protected $directory = __DIR__ . '/../Fixtures/Plugin/';

    public function testComposerList()
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
}
