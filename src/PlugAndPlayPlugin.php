<?php

namespace Dex\Composer\PlugAndPlay;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Dex\Composer\PlugAndPlay\Commands\AddCommand;
use Dex\Composer\PlugAndPlay\Commands\DumpAutoloadCommand;
use Dex\Composer\PlugAndPlay\Commands\InitCommand;
use Dex\Composer\PlugAndPlay\Commands\InstallCommand;
use Dex\Composer\PlugAndPlay\Commands\PlugAndPlayCommand;
use Dex\Composer\PlugAndPlay\Commands\ResetCommand;
use Dex\Composer\PlugAndPlay\Commands\RunCommand;
use Dex\Composer\PlugAndPlay\Commands\UpdateCommand;

class PlugAndPlayPlugin implements Capable, CommandProvider, EventSubscriberInterface, PluginInterface
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        // Do nothing..
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // Do nothing..
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // Do nothing..
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'writeManifest',
            ScriptEvents::POST_UPDATE_CMD => 'writeManifest',
            ScriptEvents::POST_AUTOLOAD_DUMP => 'writeManifest',
        ];
    }

    /**
     * Keeps packages/plug-and-play.php in sync with the lock files.
     */
    public function writeManifest(Event $event): void
    {
        Manifest::write();
    }

    public function getCapabilities(): array
    {
        return [
            CommandProvider::class => self::class,
        ];
    }

    public function getCommands(): array
    {
        return [
            new PlugAndPlayCommand(),
            new InstallCommand(),
            new UpdateCommand(),
            new DumpAutoloadCommand(),
            new AddCommand(),
            new InitCommand(),
            new ResetCommand(),
            new RunCommand(),
        ];
    }
}
