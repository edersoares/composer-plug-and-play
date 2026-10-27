<?php

beforeEach()
    ->fixtures('installer')
    ->prepare();

afterEach()
    ->cleanup();

/**
 * Moves the fixture package to the given directory, listing it as a plug and
 * play directory when it is not the packages one.
 */
function plugFrom(string $directory): void
{
    if ($directory === 'packages') {
        return;
    }

    $path = $directory === 'absolute' ? getcwd() . '/libraries' : $directory;

    mkdir('libraries/dex', recursive: true);
    rename('packages/dex/fake', 'libraries/dex/fake');

    file_put_contents('packages/composer.json', json_encode([
        'config' => ['allow-plugins' => true],
        'extra' => ['composer-plug-and-play' => ['directories' => [$path]]],
    ]));
}

dataset('directories', [
    'packages' => ['packages', '../../packages/dex/fake'],
    'extra directory' => ['libraries', '../../libraries/dex/fake'],
    'extra directory by its absolute path' => ['absolute', '../../libraries/dex/fake'],
]);

test('install-path points directly to the plugged package directory', function (string $directory, string $installPath) {
    plugFrom($directory);

    $this->runCommand('plug-and-play:install');

    $installed = json_decode(file_get_contents('vendor/composer/installed.json'), true);

    expect($installed['packages'][0]['install-path'])->toBe($installPath);
})->with('directories');

test('no symlink created in vendor directory', function (string $directory) {
    plugFrom($directory);

    $this->runCommand('plug-and-play:install');

    expect(is_link('vendor/dex/fake'))->toBeFalse()
        ->and(is_dir('vendor/dex/fake'))->toBeFalse();
})->with('directories');
