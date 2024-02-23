<?php

namespace Dex\Composer\PlugAndPlay;

interface PlugAndPlayInterface
{
    public const FILENAME = 'packages/plug-and-play.json';

    public const PACKAGES_FILE = 'packages/composer.json';

    public const PACKAGES_PATH = 'packages';

    public const PACKAGES_VENDOR = 'packages/vendor';

    public const PATH = 'packages/*/*/composer.json';
}
