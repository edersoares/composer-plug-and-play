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
    }
}
