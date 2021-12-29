<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\Console\Application as ComposerApplication;
use Dex\Composer\PlugAndPlay\Commands\ComposerCreator;
use Dex\Composer\PlugAndPlay\PlugAndPlayPlugin;

class Application extends ComposerApplication
{
    use ComposerCreator {
        getComposer as createComposer;
    }

    /**
     * @inheritDoc
     */
    public function getComposer($required = true, $disablePlugins = null, $disableScripts = null)
    {
        $composer = $this->createComposer($required, $disablePlugins, $disableScripts);

        $composer->getPluginManager()->addPlugin(new PlugAndPlayPlugin());

        return $composer;
    }
}
