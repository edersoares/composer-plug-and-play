<?php

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\BufferIO;
use Composer\Package\Locker;
use Composer\Package\Package;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\PluginManager;
use Dex\Composer\PlugAndPlay\PlugAndPlayPlugin;
use Symfony\Component\Console\Output\OutputInterface;

beforeEach(function () {
    $locker = $this->createMock(Locker::class);
    $dispatcher = $this->createMock(EventDispatcher::class);

    $config = new Config();
    $config->merge([
        'config' => [
            'allow-plugins' => true,
        ],
    ]);

    $this->composer = new Composer();
    $this->composer->setConfig($config);
    $this->composer->setLocker($locker);
    $this->composer->setEventDispatcher($dispatcher);

    $this->io = new BufferIO('', OutputInterface::VERBOSITY_DEBUG);
    $this->pm = new PluginManager($this->io, $this->composer);
})->defer(fn () => true);

test('add plugin')
    ->defer(fn () => $this->pm->addPlugin(new PlugAndPlayPlugin(), false, new Package('dex/fake', '0.0.0', '0.0.0')))
    ->expect(fn () => $this->pm->getPlugins())
    ->toHaveCount(1)
    ->and(fn () => $this->io->getOutput())
    ->toContain('Loading plugin ' . PlugAndPlayPlugin::class);

test('remove plugin')
    ->with([new PlugAndPlayPlugin()])
    ->defer(fn (PlugAndPlayPlugin $plugin) => $this->pm->addPlugin($plugin, false, new Package('dex/fake', '0.0.0', '0.0.0')))
    ->defer(fn (PlugAndPlayPlugin $plugin) => $this->pm->removePlugin($plugin))
    ->expect(fn () => $this->pm->getPlugins())
    ->toHaveCount(0)
    ->and(fn () => $this->io->getOutput())
    ->toContain('Unloading plugin ' . PlugAndPlayPlugin::class);

test('uninstall plugin')
    ->defer(fn () => $this->pm->uninstallPlugin(new PlugAndPlayPlugin()))
    ->expect(fn () => $this->pm->getPlugins())
    ->toHaveCount(0);

test('plugin capability')
    ->expect(fn () => $this->pm->getPluginCapability(new PlugAndPlayPlugin(), CommandProvider::class))
    ->toBeInstanceOf(PlugAndPlayPlugin::class);
