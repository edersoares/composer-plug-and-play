<?php

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

uses(CommandTestCase::class);

beforeEach()
    ->fixture('add-command')
    ->prepare();

afterEach()
    ->cleanup();

test('add command', function () {
    $this->runCommand('plug-and-play:add dex/extra');

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
    $this->assertPackagesFileJsonEquals([
        'require' => [
            'dex/extra' => '*',
        ],
    ]);
});

test('add command with version', function () {
    $this->runCommand('plug-and-play:add dex/extra 1.0');

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
    $this->assertPackagesFileJsonEquals([
        'require' => [
            'dex/extra' => '1.0',
        ],
    ]);
});
