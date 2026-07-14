<?php

namespace Dex\Composer\PlugAndPlay;

final class PlugAndPlayDirectories
{
    /**
     * Resolve the list of directories to scan for plug and play packages.
     * The default packages directory is always included.
     *
     * @param array $extra The root package "extra" config.
     *
     * @return string[]
     */
    public static function resolve(array $extra): array
    {
        $directories = $extra['composer-plug-and-play']['directories'] ?? [];

        array_unshift($directories, PlugAndPlayInterface::PACKAGES_PATH);

        return array_values(array_unique($directories));
    }
}
