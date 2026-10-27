<?php

namespace Dex\Composer\PlugAndPlay;

/**
 * Writes a manifest telling which packages are present only because of plug
 * and play, and which of them plug-and-play:unplug is currently hiding.
 */
class Manifest implements PlugAndPlayInterface
{
    /**
     * Writes the manifest file.
     */
    public static function write(): void
    {
        if (is_dir(self::PACKAGES_PATH) === false) {
            return;
        }

        [$plugged, $ignored] = static::pluggedAndIgnoredPackages();

        $installed = static::installed();
        $unplugged = Snapshot::unplugged(static::vendorDir());

        sort($plugged);
        sort($ignored);
        sort($unplugged);

        $data = [
            'plugged' => array_values($plugged),
            'ignored' => array_values($ignored),
            'installed' => $installed,
            'unplugged' => array_values($unplugged),
        ];

        file_put_contents(
            self::MANIFEST,
            '<?php return ' . var_export($data, true) . ';' . PHP_EOL
        );
    }

    /**
     * Packages that are present only because of plug and play: the plugged
     * ones and every dependency they brought along.
     *
     * @return string[]
     */
    public static function installed(): array
    {
        [$plugged] = static::pluggedAndIgnoredPackages();

        $installed = array_unique(array_merge($plugged, static::onlyPluggedPackages()));

        sort($installed);

        return array_values($installed);
    }

    /**
     * Vendor directory of the project, as configured in its composer.json.
     */
    public static function vendorDir(): string
    {
        return static::json('composer.json')['config']['vendor-dir'] ?? 'vendor';
    }

    /**
     * Packages found in the packages directory, split between the plugged ones
     * and the ignored ones.
     *
     * @return array{0: string[], 1: string[]}
     */
    public static function pluggedAndIgnoredPackages(): array
    {
        $ignore = array_merge(
            static::json('composer.json')['extra']['composer-plug-and-play']['ignore'] ?? [],
            static::json(self::PACKAGES_FILE)['extra']['composer-plug-and-play']['ignore'] ?? [],
        );

        $plugged = [];
        $ignored = [];

        foreach (glob(self::PATH) as $file) {
            $name = static::json($file)['name'] ?? null;

            if ($name === null) {
                continue;
            }

            if (in_array($name, $ignore, true)) {
                $ignored[] = $name;

                continue;
            }

            $plugged[] = $name;
        }

        return [$plugged, $ignored];
    }

    /**
     * Packages locked by plug and play but not by the project itself, that is,
     * the plugged packages and every dependency they brought along.
     *
     * @return string[]
     */
    private static function onlyPluggedPackages(): array
    {
        return array_diff(
            static::lockedPackages(self::LOCKFILE),
            static::lockedPackages('composer.lock')
        );
    }

    /**
     * Names of every package locked by the given lock file.
     *
     * @return string[]
     */
    private static function lockedPackages(string $file): array
    {
        $lock = static::json($file);

        $packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);

        return array_column($packages, 'name');
    }

    /**
     * Reads a JSON file, returning an empty array when it does not exist.
     */
    private static function json(string $file): array
    {
        if (is_file($file) === false) {
            return [];
        }

        return (array) json_decode((string) file_get_contents($file), true);
    }
}
