<?php

namespace Dex\Composer\PlugAndPlay\Composer;

use Composer\Composer;
use Composer\Factory as ComposerFactory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonValidationException;
use Composer\PartialComposer;
use Composer\Util\Filesystem;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use InvalidArgumentException;
use Seld\JsonLint\ParsingException;
use UnexpectedValueException;

class Factory extends ComposerFactory implements PlugAndPlayInterface
{
    private static bool $loaded = false;

    private Filesystem $filesystem;

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

        if (file_exists($lockFile)) {
            $lockData = $this->loadJsonFile($io, $lockFile);

            foreach ($lockData['packages'] ?? [] as $package) {
                $localConfig['require'][$package['name']] = $package['version'];
            }

            foreach ($lockData['packages-dev'] ?? [] as $package) {
                $localConfig['require-dev'][$package['name']] = $package['version'];
            }
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

        $allowPlugins = $packagesConfig['config']['allow-plugins'] ?? null;

        if (is_bool($allowPlugins)) {
            $localConfig['config']['allow-plugins'] = $allowPlugins;
            unset($packagesConfig['config']['allow-plugins']);
        }

        if (is_array($allowPlugins)) {
            foreach ($allowPlugins as $plugin => $allow) {
                $localConfig['config']['allow-plugins'][$plugin] = $allow;
            }
            unset($packagesConfig['config']['allow-plugins']);
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
        $requireDev = $localConfig['extra']['composer-plug-and-play']['require-dev'] ?? [];
        $autoloadDev = $localConfig['extra']['composer-plug-and-play']['autoload-dev'] ?? [];
        $strategy = $localConfig['extra']['composer-plug-and-play']['strategy'] ?? 'default';
        $isExperimental = $strategy === 'experimental:autoload';

        $packages = glob(self::PATH);

        if ($isExperimental && self::$loaded === false) {
            $cwd = getcwd();

            $realBinPath = $cwd . '/vendor/bin';
            $newBinPath = $cwd . DIRECTORY_SEPARATOR . PlugAndPlayInterface::PACKAGES_VENDOR . DIRECTORY_SEPARATOR . 'bin';

            $realComposerPath = $cwd . '/vendor/composer';
            $newComposerPath = $cwd . DIRECTORY_SEPARATOR . PlugAndPlayInterface::PACKAGES_VENDOR . DIRECTORY_SEPARATOR . 'composer';

            $realAutoloadPath = $cwd . '/vendor/autoload.php';
            $newAutoloadPath = $cwd . DIRECTORY_SEPARATOR . PlugAndPlayInterface::PACKAGES_VENDOR . DIRECTORY_SEPARATOR . 'autoload.php';

            if ($this->filesystem()->isSymlinkedDirectory($newBinPath)) {
                $this->filesystem()->unlink($newBinPath);
            }

            if ($this->filesystem()->isSymlinkedDirectory($newComposerPath)) {
                $this->filesystem()->unlink($newComposerPath);
            }

            $this->filesystem()->removeDirectoryPhp(PlugAndPlayInterface::PACKAGES_VENDOR);
            $this->filesystem()->ensureDirectoryExists(PlugAndPlayInterface::PACKAGES_VENDOR);

            $this->filesystem()->relativeSymlink($realBinPath, $newBinPath);
            $this->filesystem()->relativeSymlink($realComposerPath, $newComposerPath);
            $this->filesystem()->relativeSymlink($realAutoloadPath, $newAutoloadPath);
        }

        foreach ($packages as $package) {
            $data = $this->loadJsonFile($io, $package);

            if (in_array($data['name'], $ignore)) {
                $ignored[] = $data['name'];

                continue;
            }

            if ($isExperimental) {
                foreach ($data['autoload']['psr-4'] ?? [] as $namespace => $directory) {
                    $localConfig['autoload']['psr-4'][$namespace] = dirname($package) . DIRECTORY_SEPARATOR . $directory;
                }

                foreach ($data['autoload']['files'] ?? [] as $file) {
                    $localConfig['autoload']['files'] ??= [];
                    $localConfig['autoload']['files'][] = dirname($package) . DIRECTORY_SEPARATOR . $file;
                }

                if (in_array($data['name'], $autoloadDev)) {
                    foreach ($data['autoload-dev']['psr-4'] ?? [] as $namespace => $directory) {
                        $localConfig['autoload-dev']['psr-4'][$namespace] = dirname($package) . DIRECTORY_SEPARATOR . $directory;
                    }
                }

                $this->experimentalAutoloadStrategy($data);
            }

            // TODO show dev dependencies required
            if (in_array($data['name'], $requireDev)) {
                foreach ($data['require-dev'] ?? [] as $pack => $version) {
                    $localConfig['require-dev'][$pack] = $version;
                }
            }

            $plugged[] = $data['name'];

            $url = './' . dirname($package);

            if ($isExperimental) {
                $url = './' . str_replace('packages', PlugAndPlayInterface::PACKAGES_VENDOR, dirname($package));
            }

            $localConfig['require'][$data['name']] = '@dev';
            $localConfig['repositories'][] = $this->createRepositoryItem($url);
        }
    }

    private function filesystem(): Filesystem
    {
        return $this->filesystem ??= new Filesystem();
    }

    private function experimentalAutoloadStrategy(array $data): void
    {
        if (self::$loaded) {
            return;
        }

        $path = PlugAndPlayInterface::PACKAGES_VENDOR . DIRECTORY_SEPARATOR . $data['name'];

        $this->filesystem()->ensureDirectoryExists($path);

        unset($data['autoload']);
        unset($data['autoload-dev']);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        file_put_contents($path . '/composer.json', $json);

        $cwd = getcwd();

        $realVendorPath = $cwd . DIRECTORY_SEPARATOR . PlugAndPlayInterface::PACKAGES_VENDOR;
        $newVendorPath = $cwd . DIRECTORY_SEPARATOR . PlugAndPlayInterface::PACKAGES_PATH . DIRECTORY_SEPARATOR . $data['name'] . DIRECTORY_SEPARATOR . 'vendor';

        $this->filesystem()->relativeSymlink($realVendorPath, $newVendorPath);
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
