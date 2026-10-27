<?php

beforeEach()
    ->fixtures('unplug')
    ->prepare();

afterEach()
    ->cleanup();

function installedPackages(): array
{
    $installed = json_decode(file_get_contents('vendor/composer/installed.json'), true);

    return array_column($installed['packages'], 'name');
}

function autoloadedNamespaces(): array
{
    return array_keys(require 'vendor/composer/autoload_psr4.php');
}

test('unplug hides every plugged package from the installed repository and the autoloader', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');

    $this->assertOutputContains('Unplugged: dex/shop');

    expect($this->output)->not->toContain('Plugged packages');

    expect(installedPackages())->toBe([])
        ->and(autoloadedNamespaces())->not->toContain('Dex\\Blog\\', 'Dex\\Markdown\\', 'Dex\\Shop\\')
        ->and(file_get_contents('discovered.txt'))->toBe('')
        ->and('packages/plug-and-play.installed.json')->toBeFile()
        ->and('packages/plug-and-play.installed.php')->toBeFile();

    $this->assertManifestEquals([
        'plugged' => ['dex/blog', 'dex/markdown', 'dex/shop'],
        'ignored' => [],
        'installed' => ['dex/blog', 'dex/markdown', 'dex/shop'],
        'unplugged' => ['dex/blog', 'dex/markdown', 'dex/shop'],
    ]);
});

test('unplug keeps the given packages with their dependencies', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug --keep=dex/blog');

    expect(installedPackages())->toEqualCanonicalizing(['dex/blog', 'dex/markdown'])
        ->and(autoloadedNamespaces())->toContain('Dex\\Blog\\', 'Dex\\Markdown\\')
        ->and(autoloadedNamespaces())->not->toContain('Dex\\Shop\\')
        ->and(explode(PHP_EOL, file_get_contents('discovered.txt')))->toEqualCanonicalizing(['dex/blog', 'dex/markdown']);
});

test('unplug starts from the full repository every time', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');
    $this->runCommand('plug-and-play:unplug --keep=dex/shop');

    expect(installedPackages())->toBe(['dex/shop']);
});

test('any plug and play command brings back what unplug hid', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');
    $this->runCommand('plug-and-play:dump');

    expect(installedPackages())->toEqualCanonicalizing(['dex/blog', 'dex/markdown', 'dex/shop'])
        ->and(autoloadedNamespaces())->toContain('Dex\\Blog\\', 'Dex\\Markdown\\', 'Dex\\Shop\\')
        ->and('packages/plug-and-play.installed.json')->not->toBeFile();

    $this->assertManifestEquals([
        'plugged' => ['dex/blog', 'dex/markdown', 'dex/shop'],
        'ignored' => [],
        'installed' => ['dex/blog', 'dex/markdown', 'dex/shop'],
        'unplugged' => [],
    ]);
});

test('reset brings back the installed repository and forgets the copy', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');
    $this->runCommand('plug-and-play:reset');

    expect(installedPackages())->toEqualCanonicalizing(['dex/blog', 'dex/markdown', 'dex/shop'])
        ->and('packages/plug-and-play.installed.json')->not->toBeFile()
        ->and('packages/plug-and-play.installed.php')->not->toBeFile();
});
