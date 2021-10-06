<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Symfony\Component\Console\Input\InputOption;

trait CommandNaming
{
    /**
     * Define name, aliases and add plug and play option.
     *
     * @param string $command
     * @param array  $aliases
     *
     * @return void
     */
    public function naming(string $command, array $aliases = [])
    {
        $this->setName($command);
        $this->setAliases($aliases);
        $this->addOption('plug-and-play', null, InputOption::VALUE_NONE, 'Run using plug and play plugin.');
        $this->addOption('plug-and-play-pretend', null, InputOption::VALUE_NONE, 'Run pretending to use plug and play plugin.');
    }
}
