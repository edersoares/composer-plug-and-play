<?php

namespace Dex\Composer\PlugAndPlay;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Dex\Composer\PlugAndPlay\Commands\DumpAutoloadCommand;
use Dex\Composer\PlugAndPlay\Commands\InstallCommand;
use Dex\Composer\PlugAndPlay\Commands\PlugAndPlayCommand;
use Dex\Composer\PlugAndPlay\Commands\UpdateCommand;

class PlugAndPlayPlugin implements Capable, CommandProvider, PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public function getCapabilities()
    {
        return [
            CommandProvider::class => self::class,
        ];
    }

    public function getCommands()
    {
        return [
            new PlugAndPlayCommand(),
            new InstallCommand(),
            new UpdateCommand(),
            new DumpAutoloadCommand(),
        ];
    }
}
