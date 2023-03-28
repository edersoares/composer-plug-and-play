<?php

namespace Dex\Composer\PlugAndPlay\Tests\Commands;

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

class AddCommandTest extends CommandTestCase
{
    protected function fixture(): string
    {
        return 'add-command';
    }

    public function testAddCommand(): void
    {
        $this->runCommand('plug-and-play:add dex/extra');

        $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
        $this->assertPackagesFileJsonEquals([
            'require' => [
                'dex/extra' => '*',
            ],
        ]);
    }

    public function testAddCommandWithVersion(): void
    {
        $this->runCommand('plug-and-play:add dex/extra 1.0');

        $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
        $this->assertPackagesFileJsonEquals([
            'require' => [
                'dex/extra' => '1.0',
            ],
        ]);
    }
}
