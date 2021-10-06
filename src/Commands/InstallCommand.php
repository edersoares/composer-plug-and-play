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
    }
}
