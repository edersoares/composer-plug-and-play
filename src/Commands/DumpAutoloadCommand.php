<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\DumpAutoloadCommand as ComposerDumpAutoloadCommand;

class DumpAutoloadCommand extends ComposerDumpAutoloadCommand
{
    use CommandNaming, ComposerCreator;

    protected function configure()
    {
        parent::configure();

        $this->naming('plug-and-play:dump');
        $this->setDescription('Dumps the autoloader with plug and play dependencies.');
    }
}
