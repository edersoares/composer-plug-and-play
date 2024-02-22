<?php

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;

uses(CommandTestCase::class);

beforeEach()
    ->fixture('command')
    ->prepare();

afterEach()
    ->cleanup();

test('install command', function () {
    $this->runCommand('plug-and-play:install');

    expect($this->path() . $this->fixture . '/packages/plug-and-play.json')->toBeFile();
    expect($this->path() . $this->fixture . '/packages/plug-and-play.lock')->toBeFile();

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
});
