<?php

beforeEach()
    ->fixture('command')
    ->prepare();

afterEach()
    ->cleanup();

test('plug and play command', function () {
    $this->runCommand('plug-and-play'); // Runs UpdateCommand
    $this->runCommand('plug-and-play'); // Runs InstallCommand

    expect($this->path() . $this->fixture . '/packages/plug-and-play.json')->toBeFile();
    expect($this->path() . $this->fixture . '/packages/plug-and-play.lock')->toBeFile();

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
});
