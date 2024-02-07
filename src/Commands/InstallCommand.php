<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\InstallCommand as ComposerInstallCommand;

class InstallCommand extends ComposerInstallCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:install', 'pp:install');
        $this->setDescription('Installs the project dependencies from the plug-and-play.lock file if present, or falls back on the plug-and-play.json');
    }
}
