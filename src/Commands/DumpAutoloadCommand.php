<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\DumpAutoloadCommand as ComposerDumpAutoloadCommand;

class DumpAutoloadCommand extends ComposerDumpAutoloadCommand
{
    use ComposerCreator, CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:dump');
        $this->setDescription('Dumps the autoloader with plug and play dependencies');
    }
}
