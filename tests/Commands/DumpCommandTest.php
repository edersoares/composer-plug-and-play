<?php

namespace Dex\Composer\PlugAndPlay\Tests\Commands;

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

class DumpCommandTest extends CommandTestCase
{
    protected function fixture(): string
    {
        return 'command';
    }

    public function testDumpCommand(): void
    {
        $this->runCommand('plug-and-play:dump');

        $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
    }
}
