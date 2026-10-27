<?php

beforeEach()
    ->fixtures('unplug-shared')
    ->prepare();

afterEach()
    ->cleanup();

test('unplug keeps what a package of the project still requires', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');

    $installed = json_decode(file_get_contents('vendor/composer/installed.json'), true);

    expect(array_column($installed['packages'], 'name'))->toEqualCanonicalizing(['dex/app', 'dex/markdown']);
});

test('unplug lists the packages that stay plugged and the unplugged ones, without their dependencies', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug --keep=dex/blog');

    $this->assertOutputContains('Plugged: dex/blog');
    $this->assertOutputContains('Plugged: dex/markdown');
    $this->assertOutputContains('Unplugged: dex/shop');

    expect($this->output)->not->toContain('Plugged: dex/shop')
        ->and($this->output)->not->toContain('dex/text');
});
