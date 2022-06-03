<?php

namespace Dex\Composer\PlugAndPlay;

interface PlugAndPlayInterface
{
    const FILENAME = 'composer-plug-and-play.json';

    const PACKAGES_FILE = 'packages/composer.json';

    const PATH = 'packages/*/*/composer.json';
}
