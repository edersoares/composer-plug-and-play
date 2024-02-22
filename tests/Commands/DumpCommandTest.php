<?php

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

uses(CommandTestCase::class);

beforeEach()
    ->fixture('command')
    ->prepare();

afterEach()
    ->cleanup();

test('dump command', function () {
    $this->runCommand('plug-and-play:dump');

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
});
