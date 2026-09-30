<?php

namespace Dex\Composer\PlugAndPlay;

use Composer\Composer;
use Composer\Factory;
use Composer\Json\JsonFile;
use Seld\JsonLint\ParsingException;

/**
 * Writes a manifest telling which packages are present only because of plug
 * and play. Applications can read it to behave as if those packages were not
 * installed, without having to reset the plug and play files.
 */
class Manifest implements PlugAndPlayInterface
{
    /**
     * Writes the manifest file for the given Composer instance.
     *
     * Nothing is written while the project has no plug and play lock file,
     * that is, before the first plug and play install or after a reset.
     */
    public static function write(Composer $composer): void
    {
        if (is_file(self::LOCKFILE) === false) {
            return;
        }

        [$plugged, $ignored] = static::pluggedAndIgnoredPackages();

        $installed = static::installedOnlyByPlugAndPlay($composer, $plugged);

        sort($plugged);
        sort($ignored);
        sort($installed);

        $data = [
            'plugged' => array_values($plugged),
            'ignored' => array_values($ignored),
            'installed' => array_values($installed),
        ];

        file_put_contents(
            self::MANIFEST,
            '<?php return ' . var_export($data, true) . ';' . PHP_EOL
        );
    }

    /**
     * Packages plugged by plug and play, split between the plugged ones and
     * the ignored ones. Mirrors what the Factory does: every package found in
     * the packages directory plus every requirement of packages/composer.json.
     *
     * @return array{0: string[], 1: string[]}
     */
    private static function pluggedAndIgnoredPackages(): array
    {
        $packagesConfig = static::json(self::PACKAGES_FILE);

        $ignore = array_merge(
            static::json(Factory::getComposerFile())['extra']['composer-plug-and-play']['ignore'] ?? [],
            $packagesConfig['extra']['composer-plug-and-play']['ignore'] ?? [],
        );

        $plugged = array_keys($packagesConfig['require'] ?? []);
        $ignored = [];

        foreach (glob(self::PATH) ?: [] as $file) {
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

        return [array_unique($plugged), $ignored];
    }

    /**
     * Packages present in vendor that the project's own lock file does not
     * lock, that is, the plugged packages and every dependency they brought
     * along. A plugged package that overrides a locked one is left out, since
     * resetting would not remove it from vendor.
     *
     * When the project has no readable lock file there is no way to tell the
     * project's dependencies from the plugged ones, so only the plugged
     * packages themselves are reported.
     *
     * @param  string[]  $plugged
     * @return string[]
     */
    private static function installedOnlyByPlugAndPlay(Composer $composer, array $plugged): array
    {
        $installed = [];

        foreach ($composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages() as $package) {
            $installed[] = $package->getName();
        }

        $locked = static::projectLockedPackages();

        if ($locked === null) {
            return array_intersect($installed, $plugged);
        }

        return array_diff($installed, $locked);
    }

    /**
     * Names of every package locked by the project's own lock file, or null
     * when that file is missing or cannot be parsed.
     *
     * @return string[]|null
     */
    private static function projectLockedPackages(): ?array
    {
        $file = new JsonFile(Factory::getLockFile(Factory::getComposerFile()));

        if ($file->exists() === false) {
            return null;
        }

        try {
            $lock = $file->read();
        } catch (ParsingException) {
            return null;
        }

        $packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);

        return array_column($packages, 'name');
    }

    /**
     * Reads a JSON file, returning an empty array when it does not exist or
     * cannot be parsed.
     */
    private static function json(string $file): array
    {
        $json = new JsonFile($file);

        if ($json->exists() === false) {
            return [];
        }

        try {
            return (array) $json->read();
        } catch (ParsingException) {
            return [];
        }
    }
}
