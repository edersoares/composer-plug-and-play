<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\InstallCommand as ComposerInstallCommand;

class InstallCommand extends ComposerInstallCommand
{
    use CommandNaming, ComposerCreator;

    protected function configure()
    {
        parent::configure();

        $this->naming('plug-and-play:install');
        $this->setDescription('Installs the project dependencies from the plug-and-play.lock file if present, or falls back on the plug-and-play.json.');
    }
}
