<?php

use Dex\Composer\PlugAndPlay\Manifest;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;

const DEX_BLOG = 'dex/blog';
const DEX_MARKDOWN = 'dex/markdown';
const DEX_SHOP = 'dex/shop';
const DEX_PACKAGES = [DEX_BLOG, DEX_MARKDOWN, DEX_SHOP];

const DEX_BLOG_NAMESPACE = 'Dex\\Blog\\';
const DEX_MARKDOWN_NAMESPACE = 'Dex\\Markdown\\';
const DEX_SHOP_NAMESPACE = 'Dex\\Shop\\';

const SNAPSHOT_JSON = PlugAndPlayInterface::SNAPSHOT . '.json';
const SNAPSHOT_PHP = PlugAndPlayInterface::SNAPSHOT . '.php';

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
    // The file changes between commands, so it must be read again every time.
    return array_keys(require 'vendor/composer/autoload_psr4.php'); // NOSONAR
}

test('unplug hides every plugged package from the installed repository and the autoloader', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');

    $this->assertOutputContains('Unplugged: ' . DEX_SHOP);

    expect($this->output)->not->toContain('Plugged packages');

    expect(installedPackages())->toBe([])
        ->and(autoloadedNamespaces())->not->toContain(DEX_BLOG_NAMESPACE, DEX_MARKDOWN_NAMESPACE, DEX_SHOP_NAMESPACE)
        ->and(file_get_contents('discovered.txt'))->toBe('')
        ->and(SNAPSHOT_JSON)->toBeFile()
        ->and(SNAPSHOT_PHP)->toBeFile();

    $this->assertManifestEquals([
        'plugged' => DEX_PACKAGES,
        'ignored' => [],
        'installed' => DEX_PACKAGES,
        'unplugged' => DEX_PACKAGES,
    ]);
});

test('unplug keeps the given packages with their dependencies', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug --keep=' . DEX_BLOG);

    expect(installedPackages())->toEqualCanonicalizing([DEX_BLOG, DEX_MARKDOWN])
        ->and(autoloadedNamespaces())->toContain(DEX_BLOG_NAMESPACE, DEX_MARKDOWN_NAMESPACE)
        ->and(autoloadedNamespaces())->not->toContain(DEX_SHOP_NAMESPACE)
        ->and(explode(PHP_EOL, file_get_contents('discovered.txt')))->toEqualCanonicalizing([DEX_BLOG, DEX_MARKDOWN]);
});

test('unplug starts from the full repository every time', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');
    $this->runCommand('plug-and-play:unplug --keep=' . DEX_SHOP);

    expect(installedPackages())->toBe([DEX_SHOP]);
});

test('any plug and play command brings back what unplug hid', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');
    $this->runCommand('plug-and-play:dump');

    expect(installedPackages())->toEqualCanonicalizing(DEX_PACKAGES)
        ->and(autoloadedNamespaces())->toContain(DEX_BLOG_NAMESPACE, DEX_MARKDOWN_NAMESPACE, DEX_SHOP_NAMESPACE)
        ->and(SNAPSHOT_JSON)->not->toBeFile();

    // The plugin writes the manifest after the autoload dump, but it is not
    // registered in the Composer instance that the commands create in tests.
    Manifest::write();

    $this->assertManifestEquals([
        'plugged' => DEX_PACKAGES,
        'ignored' => [],
        'installed' => DEX_PACKAGES,
        'unplugged' => [],
    ]);
});

test('reset brings back the installed repository and forgets the copy', function () {
    $this->runCommand('plug-and-play:install');
    $this->runCommand('plug-and-play:unplug');
    $this->runCommand('plug-and-play:reset');

    expect(installedPackages())->toEqualCanonicalizing(DEX_PACKAGES)
        ->and(SNAPSHOT_JSON)->not->toBeFile()
        ->and(SNAPSHOT_PHP)->not->toBeFile();
});
