<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit;

use Composer\Composer;
use Composer\Config;
use Composer\IO\BufferIO;
use Composer\Package\Package;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\PluginManager;
use Dex\Composer\PlugAndPlay\PlugAndPlayPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class PlugAndPlayPluginTest extends TestCase
{
    /**
     * @var BufferIO
     */
    protected $io;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var PluginManager
     */
    protected $pm;

    protected function setUp(): void
    {
        $config = new Config();
        $config->merge([
            'config' => [
                'allow-plugins' => true,
            ],
        ]);

        $this->composer = new Composer();
        $this->composer->setConfig($config);

        $this->io = new BufferIO('', OutputInterface::VERBOSITY_DEBUG);
        $this->pm = new PluginManager($this->io, $this->composer);
    }

    public function testAddPlugin()
    {
        $this->pm->addPlugin(new PlugAndPlayPlugin(), false, new Package('dex/fake', '0.0.0', '0.0.0'));

        $this->assertCount(1, $this->pm->getPlugins());
        $this->assertStringContainsString('Loading plugin ' . PlugAndPlayPlugin::class, $this->io->getOutput());
    }

    public function testPluginCapability()
    {
        $capability = $this->pm->getPluginCapability(new PlugAndPlayPlugin(), CommandProvider::class);

        $this->assertInstanceOf(PlugAndPlayPlugin::class, $capability);
    }
}
