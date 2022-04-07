<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Composer\Composer;
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
    public function getComposer(bool $required = true, ?bool $disablePlugins = null, ?bool $disableScripts = null): ?Composer
    {
        $composer = $this->createComposer($required, $disablePlugins, $disableScripts);

        $composer->getPluginManager()->addPlugin(new PlugAndPlayPlugin());

        return $composer;
    }
}
