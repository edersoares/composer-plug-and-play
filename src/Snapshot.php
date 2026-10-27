<?php

namespace Dex\Composer\PlugAndPlay;

/**
 * Keeps a copy of the installed repository (vendor/composer/installed.json and
 * installed.php) taken before plug-and-play:unplug hides packages from it, so
 * any plug and play command can bring the project back to the full state.
 */
class Snapshot implements PlugAndPlayInterface
{
    private const FILES = ['installed.json', 'installed.php'];

    /**
     * Copies the installed repository files of the vendor directory.
     */
    public static function save(string $vendorDir): void
    {
        foreach (self::FILES as $file) {
            $from = static::installed($vendorDir, $file);

            if (is_file($from)) {
                copy($from, static::copy($file));
            }
        }
    }

    /**
     * Puts the saved files back in the vendor directory and forgets them.
     */
    public static function restore(string $vendorDir): void
    {
        foreach (self::FILES as $file) {
            if (is_file(static::copy($file))) {
                copy(static::copy($file), static::installed($vendorDir, $file));
            }
        }

        static::forget();
    }

    /**
     * Deletes the saved files.
     */
    public static function forget(): void
    {
        foreach (self::FILES as $file) {
            if (is_file(static::copy($file))) {
                unlink(static::copy($file));
            }
        }
    }

    /**
     * Packages that are in the saved repository but not in the current one,
     * that is, the ones hidden by plug-and-play:unplug.
     *
     * @return string[]
     */
    public static function unplugged(string $vendorDir): array
    {
        if (is_file(static::copy('installed.json')) === false) {
            return [];
        }

        return array_values(array_diff(
            static::names(static::copy('installed.json')),
            static::names(static::installed($vendorDir, 'installed.json'))
        ));
    }

    /**
     * Package names of an installed.json file.
     *
     * @return string[]
     */
    private static function names(string $file): array
    {
        if (is_file($file) === false) {
            return [];
        }

        $data = (array) json_decode((string) file_get_contents($file), true);

        return array_column($data['packages'] ?? $data, 'name');
    }

    private static function installed(string $vendorDir, string $file): string
    {
        return $vendorDir . '/composer/' . $file;
    }

    private static function copy(string $file): string
    {
        return self::SNAPSHOT . '.' . pathinfo($file, PATHINFO_EXTENSION);
    }
}
