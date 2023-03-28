<?php

namespace Dex\Composer\PlugAndPlay\Tests\Commands;

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

class InitCommandTest extends CommandTestCase
{
    protected function fixture(): string
    {
        return 'command';
    }

    public function testInitCommand(): void
    {
        $this->runCommand('plug-and-play:init');

        $this->assertFileExists($this->path() . $this->fixture() . '/packages/.gitignore');
        $this->assertFileExists($this->path() . $this->fixture() . '/packages/composer.json');
        $this->assertOutputContains('You are using Composer Plug and Play Plugin.');

        $this->runCommand('plug-and-play:init');

        $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
        $this->assertOutputContains('The [packages] directory already exists.');
        $this->assertOutputContains('The [packages/.gitignore] file already exists.');
        $this->assertOutputContains('The [packages/composer.json] file already exists.');
    }
}
