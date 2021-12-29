<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit;

use Composer\Composer;
use Composer\Config;
use Composer\IO\BufferIO;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\PluginManager;
use Dex\Composer\PlugAndPlay\PlugAndPlayPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\StreamOutput;

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
        $this->io = new BufferIO('', StreamOutput::VERBOSITY_DEBUG);
        $this->composer = new Composer();
        $this->composer->setConfig(new Config());
        $this->pm = new PluginManager($this->io, $this->composer);
    }

    public function testAddPlugin()
    {
        $this->pm->addPlugin(new PlugAndPlayPlugin());

        $this->assertCount(1, $this->pm->getPlugins());
        $this->assertStringContainsString('Loading plugin ' . PlugAndPlayPlugin::class, $this->io->getOutput());
    }

    public function testPluginCapability()
    {
        $capability = $this->pm->getPluginCapability(new PlugAndPlayPlugin(), CommandProvider::class);

        $this->assertInstanceOf(PlugAndPlayPlugin::class, $capability);
    }
}
