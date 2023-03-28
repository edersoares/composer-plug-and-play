<?php

namespace Dex\Composer\PlugAndPlay\Commands;

trait CommandNaming
{
    /**
     * Define name, aliases and add plug and play option.
     */
    public function naming(string $command): void
    {
        $this->setName($command);
        $this->setAliases([]);
    }
}
