<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Symfony\Component\Console\Input\InputOption;

trait CommandNaming
{
    /**
     * Define name, aliases and add plug and play option.
     *
     * @param string $command
     *
     * @return void
     */
    public function naming(string $command)
    {
        $this->setName($command);
        $this->setAliases([]);
        $this->addOption('plug-and-play-pretend', null, InputOption::VALUE_NONE, 'Run pretending to use plug and play plugin.');
    }
}
