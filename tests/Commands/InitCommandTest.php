<?php

beforeEach()
    ->fixture('command')
    ->prepare();

afterEach()
    ->cleanup();

test('init command', function () {
    $this->runCommand('plug-and-play:init');

    expect($this->path() . $this->fixture . '/packages/.gitignore')->toBeFile();
    expect($this->path() . $this->fixture . '/packages/composer.json')->toBeFile();

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');

    $this->runCommand('plug-and-play:init');

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
    $this->assertOutputContains('The [packages] directory already exists.');
    $this->assertOutputContains('The [packages/.gitignore] file already exists.');
    $this->assertOutputContains('The [packages/composer.json] file already exists.');
});
