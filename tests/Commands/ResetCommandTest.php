<?php

namespace Dex\Composer\PlugAndPlay\Tests\Commands;

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

class ResetCommandTest extends CommandTestCase
{
    protected function fixture(): string
    {
        return 'reset-command';
    }

    public function testAddCommand(): void
    {
        $this->runCommand('plug-and-play:reset');

        $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
        $this->assertFileDoesNotExist($this->path() . $this->fixture() . '/packages/plug-and-play.json');
        $this->assertFileDoesNotExist($this->path() . $this->fixture() . '/packages/plug-and-play.lock');
    }
}
