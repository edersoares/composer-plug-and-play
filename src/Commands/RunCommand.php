<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\RunScriptCommand as ComposerRunCommand;

class RunCommand extends ComposerRunCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:run', 'pp:run');
        $this->setDescription('Runs the scripts defined in plug and play composer.json');
    }
}
