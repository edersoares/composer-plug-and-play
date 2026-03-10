<?php

namespace Dex\Composer\PlugAndPlay;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Dex\Composer\PlugAndPlay\Commands\AddCommand;
use Dex\Composer\PlugAndPlay\Composer\Installer;
use Dex\Composer\PlugAndPlay\Commands\DumpAutoloadCommand;
use Dex\Composer\PlugAndPlay\Commands\InitCommand;
use Dex\Composer\PlugAndPlay\Commands\InstallCommand;
use Dex\Composer\PlugAndPlay\Commands\PlugAndPlayCommand;
use Dex\Composer\PlugAndPlay\Commands\ResetCommand;
use Dex\Composer\PlugAndPlay\Commands\RunCommand;
use Dex\Composer\PlugAndPlay\Commands\UpdateCommand;

class PlugAndPlayPlugin implements Capable, CommandProvider, PluginInterface
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        $composer->getInstallationManager()->addInstaller(new Installer($io, $composer));

        if (file_exists(PlugAndPlayInterface::PACKAGES_FILE)) {
            $this->mergeScripts($composer);
        }
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // Do nothing..
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // Do nothing..
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

    private function mergeScripts(Composer $composer): void
    {
        $file = file_get_contents(PlugAndPlayInterface::PACKAGES_FILE);
        $config = json_decode($file, true) ?? [];
        $scripts = $config['scripts'] ?? [];

        if (empty($scripts)) {
            return;
        }

        $rootPackage = $composer->getPackage();
        $existingScripts = $rootPackage->getScripts();

        foreach ($scripts as $name => $script) {
            $existingScripts[$name] = (array) $script;
        }

        $rootPackage->setScripts($existingScripts);
    }
}
