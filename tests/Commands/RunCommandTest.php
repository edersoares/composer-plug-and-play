<?php

beforeEach()
    ->fixtures('run-command')
    ->prepare();

afterEach()
    ->cleanup();

test('scripts from packages/composer.json are available to composer run', function () {
    $this->runCommand('run pp-script');

    $this->assertStringNotContainsString('is not defined', $this->output);
});

test('scripts from packages/composer.json override root scripts', function () {
    $this->runCommand('run root-script');

    $this->assertStringNotContainsString('is not defined', $this->output);
});
