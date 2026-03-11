<?php

beforeEach()
    ->fixtures('installer')
    ->prepare();

afterEach()
    ->cleanup();

test('install-path points directly to packages directory', function () {
    $this->runCommand('plug-and-play:install');

    $installedJsonPath = $this->path() . $this->fixture . '/vendor/composer/installed.json';

    expect($installedJsonPath)->toBeFile();

    $installed = json_decode(file_get_contents($installedJsonPath), true);
    $package = $installed['packages'][0];

    expect($package)->not->toBeNull();
    expect($package['install-path'])->toBe('../../packages/dex/fake');
});

test('no symlink created in vendor directory', function () {
    $this->runCommand('plug-and-play:install');

    $vendorPath = $this->path() . $this->fixture . '/vendor/dex/fake';

    expect(is_link($vendorPath))->toBeFalse();
    expect(is_dir($vendorPath))->toBeFalse();
});
