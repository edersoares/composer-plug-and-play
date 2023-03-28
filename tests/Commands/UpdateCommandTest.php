<?php

namespace Dex\Composer\PlugAndPlay\Tests\Commands;

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

class UpdateCommandTest extends CommandTestCase
{
    protected function fixture(): string
    {
        return 'command';
    }

    public function testInstallCommand(): void
    {
        $this->runCommand('plug-and-play:update');

        $this->assertFileExists($this->path() . $this->fixture() . '/packages/plug-and-play.json');
        $this->assertFileExists($this->path() . $this->fixture() . '/packages/plug-and-play.lock');
        $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
    }
}
