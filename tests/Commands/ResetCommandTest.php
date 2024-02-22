<?php

beforeEach()
    ->fixture('reset-command')
    ->prepare();

afterEach()
    ->cleanup();

test('add command', function () {
    $this->runCommand('plug-and-play:reset');

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
    $this->assertFileDoesNotExist($this->path() . $this->fixture . '/packages/plug-and-play.json');
    $this->assertFileDoesNotExist($this->path() . $this->fixture . '/packages/plug-and-play.lock');
});
