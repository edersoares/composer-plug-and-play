<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\DumpAutoloadCommand as ComposerDumpAutoloadCommand;

class DumpAutoloadCommand extends ComposerDumpAutoloadCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:dump', 'pp:dump');
        $this->setDescription('Dumps the autoloader with plug and play dependencies');
    }
}
