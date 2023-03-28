<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\BufferIO;
use Composer\Package\Locker;
use Composer\Package\Package;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\PluginManager;
use Dex\Composer\PlugAndPlay\PlugAndPlayPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class PlugAndPlayPluginTest extends TestCase
{
    protected BufferIO $io;

    protected Composer $composer;

    protected PluginManager $pm;

    protected function setUp(): void
    {
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
    }

    public function testAddPlugin(): void
    {
        $this->pm->addPlugin(new PlugAndPlayPlugin(), false, new Package('dex/fake', '0.0.0', '0.0.0'));

        $this->assertCount(1, $this->pm->getPlugins());
        $this->assertStringContainsString('Loading plugin ' . PlugAndPlayPlugin::class, $this->io->getOutput());
    }

    public function testRemovePlugin(): void
    {
        $plugin = new PlugAndPlayPlugin();

        $this->pm->addPlugin($plugin, false, new Package('dex/fake', '0.0.0', '0.0.0'));
        $this->pm->removePlugin($plugin);

        $this->assertCount(0, $this->pm->getPlugins());
        $this->assertStringContainsString('Unloading plugin ' . PlugAndPlayPlugin::class, $this->io->getOutput());
    }

    public function testUninstallPlugin(): void
    {
        $this->pm->uninstallPlugin(new PlugAndPlayPlugin());

        $this->assertCount(0, $this->pm->getPlugins());
    }

    public function testPluginCapability(): void
    {
        $capability = $this->pm->getPluginCapability(new PlugAndPlayPlugin(), CommandProvider::class);

        $this->assertInstanceOf(PlugAndPlayPlugin::class, $capability);
    }
}
