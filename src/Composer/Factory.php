<?php

namespace Dex\Composer\PlugAndPlay\Composer;

use Composer\Composer;
use Composer\Factory as ComposerFactory;
use Composer\Installer;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonValidationException;
use Composer\PartialComposer;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;
use Dex\Composer\PlugAndPlay\Composer\Installer as PlugAndPlayInstaller;
use Dex\Composer\PlugAndPlay\Manifest;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Dex\Composer\PlugAndPlay\Snapshot;
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
     * Skips the list of plugged and ignored packages, for a command that
     * writes it by itself.
     */
    public static function silence(): void
    {
        static::$loaded = true;
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
    private function createRepositoryItem(string $url): array
    {
        return [
            'type' => 'path',
            'url' => $url,
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

        // Every plug and play command starts from the full installed
        // repository, bringing back what plug-and-play:unplug hid.
        Snapshot::restore($localConfig['config']['vendor-dir'] ?? 'vendor');

        if (file_exists($lockFile)) {
            $lockData = $this->loadJsonFile($io, $lockFile);

            foreach ($lockData['packages'] ?? [] as $package) {
                $localConfig['require'][$package['name']] = $package['version'];
            }

            foreach ($lockData['packages-dev'] ?? [] as $package) {
                $localConfig['require-dev'][$package['name']] = $package['version'];
            }
        }

        $originalRepositories = $localConfig['repositories'] ?? [];

        $localConfig = $this->mergeComposerPackageFile($io, $localConfig);

        [$local] = Manifest::localPackages();

        foreach ($local as $package => $directory) {
            $localConfig = $this->plugPackage($io, $localConfig, $package, $directory);
        }

        if (static::$loaded === false) {
            [$plugged, $ignored] = Manifest::pluggedAndIgnoredPackages();

            static::writePackages($io, $plugged, $ignored);
        }

        static::$loaded = true;

        $repositories = array_merge($localConfig['repositories'] ?? [], $originalRepositories);

        if ($repositories) {
            $localConfig['repositories'] = $repositories;
        } else {
            unset($localConfig['repositories']);
        }

        if ($fullLoad) {
            $this->saveComposerPlugAndPlayFile($localConfig);
        }

        return parent::createComposer($io, self::FILENAME, $disablePlugins, $cwd, $fullLoad, $disableScripts);
    }

    /**
     * Merges the plug and play composer.json file and plugs its dependencies.
     *
     * @throws JsonValidationException
     * @throws ParsingException
     */
    private function mergeComposerPackageFile(IOInterface $io, array $localConfig): array
    {
        $packagesConfig = file_exists(self::PACKAGES_FILE)
            ? $this->loadJsonFile($io, self::PACKAGES_FILE)
            : [];

        unset($localConfig['repositories']);

        $localConfig = array_merge_recursive($localConfig, $packagesConfig);

        if (isset($packagesConfig['minimum-stability'])) {
            $localConfig['minimum-stability'] = $packagesConfig['minimum-stability'];
        }

        if (isset($packagesConfig['prefer-stable'])) {
            $localConfig['prefer-stable'] = $packagesConfig['prefer-stable'];
        }

        $allowPlugins = $packagesConfig['config']['allow-plugins'] ?? null;

        if (is_bool($allowPlugins)) {
            $localConfig['config']['allow-plugins'] = $allowPlugins;
        }

        if (is_array($allowPlugins)) {
            foreach ($allowPlugins as $plugin => $allow) {
                $localConfig['config']['allow-plugins'][$plugin] = $allow;
            }
        }

        foreach ($packagesConfig['require'] ?? [] as $package => $version) {
            $localConfig = $this->overridePackage($package, $localConfig);

            $localConfig['require'][$package] = $version;
        }

        return $localConfig;
    }

    /**
     * Links a plug and play package to its directory.
     *
     * @throws JsonValidationException
     * @throws ParsingException
     */
    private function plugPackage(IOInterface $io, array $localConfig, string $package, string $directory): array
    {
        $data = $this->loadJsonFile($io, $directory . DIRECTORY_SEPARATOR . 'composer.json');

        $requireDev = $localConfig['extra']['composer-plug-and-play']['require-dev'] ?? [];
        $autoloadDev = $localConfig['extra']['composer-plug-and-play']['autoload-dev'] ?? [];

        $localConfig = $this->overridePackage($package, $localConfig);

        if (in_array($package, $requireDev)) {
            foreach ($data['require-dev'] ?? [] as $pack => $version) {
                $localConfig['require-dev'][$pack] = $version;
            }
        }

        if (in_array($package, $autoloadDev)) {
            foreach ($data['autoload-dev']['psr-4'] ?? [] as $namespace => $path) {
                $localConfig['autoload-dev']['psr-4'][$namespace] = $directory . DIRECTORY_SEPARATOR . $path;
            }
        }

        $localConfig['require'][$package] = '@dev';
        $url = (new Filesystem())->isAbsolutePath($directory) ? $directory : './' . $directory;

        $localConfig['repositories'][] = $this->createRepositoryItem($url);

        return $localConfig;
    }

    private function overridePackage(string $package, array $localConfig): array
    {
        unset($localConfig['require'][$package]);
        unset($localConfig['require-dev'][$package]);

        return $localConfig;
    }

    /**
     * Write in output which packages are plugged, ignored and unplugged.
     */
    public static function writePackages(IOInterface $io, array $plugged, array $ignored, array $unplugged = []): void
    {
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

        if ($unplugged) {
            sort($unplugged);

            $io->write('<info>Unplugged packages</info>');
        }

        foreach ($unplugged as $package) {
            $io->write("  Unplugged: $package");
        }
    }

    protected function createDefaultInstallers(Installer\InstallationManager $im, PartialComposer $composer, IOInterface $io, ?ProcessExecutor $process = null): void
    {
        parent::createDefaultInstallers($im, $composer, $io, $process);

        $im->addInstaller(new PlugAndPlayInstaller($io, $composer));
    }
}
