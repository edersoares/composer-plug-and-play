<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\Composer;
use Composer\Console\Application as ComposerApplication;
use Composer\Package\Package;
use Dex\Composer\PlugAndPlay\PlugAndPlayPlugin;

class Application extends ComposerApplication
{
    public function getComposer(
        bool $required = true,
        ?bool $disablePlugins = null,
        ?bool $disableScripts = null,
    ): ?Composer {
        $composer = parent::getComposer(true, $disablePlugins, $disableScripts);

        $composer->getPluginManager()->addPlugin(new PlugAndPlayPlugin(), false, new Package('dex/fake', '0.0.0', '0.0.0'));

        return $composer;
    }
}
