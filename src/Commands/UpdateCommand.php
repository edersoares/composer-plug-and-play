<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\UpdateCommand as ComposerUpdateCommand;

class UpdateCommand extends ComposerUpdateCommand
{
    use CommandNaming, ComposerCreator;

    protected function configure()
    {
        parent::configure();

        $this->naming('plug-and-play:update');
        $this->setDescription('Updates your dependencies to the latest version according to plug-and-play.json, and updates the plug-and-play.lock file.');
    }
}
