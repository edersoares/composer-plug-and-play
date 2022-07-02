<?php

namespace Dex\Composer\PlugAndPlay;

interface PlugAndPlayInterface
{
    const FILENAME = 'packages/plug-and-play.json';

    const PACKAGES_FILE = 'packages/composer.json';

    const PACKAGES_PATH = 'packages';

    const PATH = 'packages/*/*/composer.json';
}
