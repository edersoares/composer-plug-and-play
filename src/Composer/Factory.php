<?php

namespace Dex\Composer\PlugAndPlay\Composer;

use Composer\Composer;
use Composer\Factory as ComposerFactory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonValidationException;
use Composer\PartialComposer;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use InvalidArgumentException;
use Seld\JsonLint\ParsingException;
use UnexpectedValueException;

class Factory extends ComposerFactory implements PlugAndPlayInterface
{
    private static bool $loaded = false;

    /**
     * Restart factory.
     */
    public static function restart(): void
    {
        static::$loaded = false;
    }

    /**
     * Loads Composer JSON file if it has a validated schema.
     *
     * @throws JsonValidationException
     * @throws ParsingException
     */
    private function loadJsonFile(IOInterface $io, string $filename): mixed
    {
        $file = new JsonFile($filename, null, $io);

        $file->validateSchema(JsonFile::LAX_SCHEMA);

        return $file->read();
    }

    /**
     * Saves plug-and-play.json and plug-and-play.lock files.
     */
    private function saveComposerPlugAndPlayFile(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        if (is_dir(self::PACKAGES_PATH) === false) {
            mkdir(self::PACKAGES_PATH);
        }

        file_put_contents(self::FILENAME, $json);
    }

    /**
     * Creates a repository item.
     */
    private function createRepositoryItem(string $package): array
    {
        return [
            'type' => 'path',
            'url' => './' . dirname($package),
            'symlink' => true
        ];
    }

    /**
     * Creates a Composer instance.
     *
     * @throws InvalidArgumentException
     * @throws JsonValidationException
     * @throws UnexpectedValueException
     * @throws ParsingException
     */
    public function createComposer(
        IOInterface $io,
        $localConfig = null,
        $disablePlugins = false,
        $cwd = null,
        $fullLoad = true,
        $disableScripts = false
    ): Composer|PartialComposer {
        $cwd = $cwd ?: getcwd();

        if (null === $localConfig) {
            $localConfig = static::getComposerFile();
        }

        $lockFile = static::getLockFile($localConfig);

        if (is_string($localConfig)) {
            $localConfig = $this->loadJsonFile($io, $localConfig);
        }

        $lockData = $this->loadJsonFile($io, $lockFile);

        foreach ($lockData['packages'] ?? [] as $package) {
            $localConfig['require'][$package['name']] = $package['version'];
        }

        foreach ($lockData['packages-dev'] ?? [] as $package) {
            $localConfig['require-dev'][$package['name']] = $package['version'];
        }

        $ignored = [];
        $plugged = [];

        $this->loadComposerPackageFile($io, $plugged, $localConfig);
        $this->definePluggedAndIgnoredPackages($io, $plugged, $ignored, $localConfig);
        $this->writePluggedAndIgnoredPackages($io, $plugged, $ignored);

        if ($fullLoad) {
            $this->saveComposerPlugAndPlayFile($localConfig);
        }

        return parent::createComposer($io, self::FILENAME, $disablePlugins, $cwd, $fullLoad, $disableScripts);
    }

    /**
     * Load composer.json plug and play file and plugs its dependencies.
     *
     * @throws JsonValidationException
     * @throws ParsingException
     */
    private function loadComposerPackageFile(IOInterface $io, array &$plugged, array &$localConfig): void
    {
        $packagesConfig = file_exists(self::PACKAGES_FILE)
            ? $this->loadJsonFile($io, self::PACKAGES_FILE)
            : [];

        foreach ($packagesConfig['require'] ?? [] as $package => $version) {
            $plugged[] = $package;
        }

        # TODO improve next lines
        if (isset($packagesConfig['minimum-stability'])) {
            $localConfig['minimum-stability'] = $packagesConfig['minimum-stability'];
            unset($packagesConfig['minimum-stability']);
        }

        if (isset($packagesConfig['prefer-stable'])) {
            $localConfig['prefer-stable'] = $packagesConfig['prefer-stable'];
            unset($packagesConfig['prefer-stable']);
        }

        $localConfig = array_merge_recursive($localConfig, $packagesConfig);
    }

    /**
     * Define plugged and ignored packages to list after and prepare plug and
     * play packages to link to its repository.
     *
     * @throws JsonValidationException
     * @throws ParsingException
     */
    private function definePluggedAndIgnoredPackages(IOInterface $io, array &$plugged, array &$ignored, array &$localConfig): void
    {
        $ignore = $localConfig['extra']['composer-plug-and-play']['ignore'] ?? [];

        $packages = glob(self::PATH);

        foreach ($packages as $package) {
            $data = $this->loadJsonFile($io, $package);

            if (in_array($data['name'], $ignore)) {
                $ignored[] = $data['name'];

                continue;
            }

            $plugged[] = $data['name'];

            $localConfig['require'][$data['name']] = '@dev';
            $localConfig['repositories'][] = $this->createRepositoryItem($package);
        }
    }

    /**
     * Write in output which packages are plugged and witch are ignored.
     */
    private function writePluggedAndIgnoredPackages(IOInterface $io, array $plugged, array $ignored): void
    {
        if (static::$loaded) {
            return;
        }

        if ($plugged) {
            sort($plugged);

            $io->write('<info>Plugged packages</info>');
        }

        foreach ($plugged as $package) {
            $io->write("  Plugged: <info>$package</info>");
        }

        if ($ignored) {
            sort($ignored);

            $io->write('<info>Ignored packages</info>');
        }

        foreach ($ignored as $package) {
            $io->write("  Ignored: $package");
        }

        static::$loaded = true;
    }
}
