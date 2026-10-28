<?php

use Composer\Config;
use Composer\IO\NullIO;
use Composer\Package\Package;
use Composer\PartialComposer;
use Composer\Repository\InstalledArrayRepository;
use Dex\Composer\PlugAndPlay\Composer\Installer;

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

function installer(): Installer
{
    $composer = new PartialComposer();
    $composer->setConfig(new Config(false, getcwd()));

    return new Installer(new NullIO(), $composer);
}

function plugAndPlayPackage(string $version = '1.0.0'): Package
{
    $package = new Package('dex/fake', $version, $version);
    $package->setDistUrl('./packages/dex/fake');

    return $package;
}

test('update replaces a plugged package in the repository without touching its directory', function () {
    $repo = new InstalledArrayRepository([$initial = plugAndPlayPackage()]);
    $target = plugAndPlayPackage('2.0.0');

    installer()->update($repo, $initial, $target);

    expect($repo->getPackages())->toHaveCount(1)
        ->and($repo->getPackages()[0]->getVersion())->toBe('2.0.0')
        ->and('packages/dex/fake/composer.json')->toBeFile();
});

test('uninstall removes a plugged package from the repository without deleting its directory', function () {
    $repo = new InstalledArrayRepository([$package = plugAndPlayPackage()]);

    installer()->uninstall($repo, $package);

    expect($repo->getPackages())->toBe([])
        ->and('packages/dex/fake/composer.json')->toBeFile();
});

test('packages without dist url are installed in the vendor directory', function () {
    $package = new Package('dex/other', '1.0.0', '1.0.0');

    expect(installer()->getInstallPath($package))->toBe(getcwd() . '/vendor/dex/other');
});

test('update and uninstall of other packages are left to composer', function (string $method, array $packages) {
    installer()->$method(new InstalledArrayRepository(), ...$packages);
})->with([
    'update' => ['update', [new Package('dex/other', '1.0.0', '1.0.0'), new Package('dex/other', '2.0.0', '2.0.0')]],
    'uninstall' => ['uninstall', [new Package('dex/other', '1.0.0', '1.0.0')]],
])->throws(InvalidArgumentException::class, 'Package is not installed');
