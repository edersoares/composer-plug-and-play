<?php

namespace Dex\Composer\PlugAndPlay\Composer;

use Composer\Composer;
use Composer\Factory as ComposerFactory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonValidationException;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use InvalidArgumentException;
use UnexpectedValueException;

class Factory extends ComposerFactory implements PlugAndPlayInterface
{
    /**
     * @var bool
     */
    private static $loaded = false;

    /**
     * Restart factory.
     *
     * @return void
     */
    public static function restart(): void
    {
        static::$loaded = false;
    }

    /**
     * Loads Composer JSON file if has a validated schema.
     *
     * @param IOInterface $io
     * @param string      $filename
     *
     * @throws JsonValidationException
     *
     * @return mixed
     */
    private function loadJsonFile(IOInterface $io, string $filename)
    {
        $file = new JsonFile($filename, null, $io);

        $file->validateSchema(JsonFile::LAX_SCHEMA);

        return $file->read();
    }

    /**
     * Saves plug-and-play.json and plug-and-play.lock files.
     *
     * @param array $data
     *
     * @return void
     */
    private function saveComposerPlugAndPlayFile(array $data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        if (is_dir(self::PACKAGES_PATH) === false) {
            mkdir(self::PACKAGES_PATH);
        }

        file_put_contents(self::FILENAME, $json);
    }

    /**
     * Creates a repository item.
     *
     * @param string $package
     *
     * @return array
     */
    private function createRepositoryItem(string $package)
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
     * @param IOInterface $io
     * @param string|null $localConfig
     * @param false       $disablePlugins
     * @param string|null $cwd
     * @param bool        $fullLoad
     *
     * @throws InvalidArgumentException
     * @throws JsonValidationException
     * @throws UnexpectedValueException
     *
     * @return Composer
     */
    public function createComposer(
        IOInterface $io,
        $localConfig = null,
        $disablePlugins = false,
        $cwd = null,
        $fullLoad = true,
        $disableScripts = false
    ) {
        $cwd = $cwd ?: getcwd();

        $ignored = [];
        $plugged = [];

        if (null === $localConfig) {
            $localConfig = static::getComposerFile();
        }

        if (is_string($localConfig)) {
            $localConfig = $this->loadJsonFile($io, $localConfig);
        }

        if (file_exists(self::PACKAGES_FILE)) {
            $packagesConfig = $this->loadJsonFile($io, self::PACKAGES_FILE);
            $required = $packagesConfig['require'] ?? [];

            foreach ($required as $package => $version) {
                $plugged[] = $package;
            }

            $localConfig = array_merge_recursive($localConfig, $packagesConfig);
        }

        $ignore = $localConfig['extra']['composer-plug-and-play']['ignore'] ?? [];

        $packages = glob(self::PATH);

        foreach ($packages as $package) {
            $data = $this->loadJsonFile($io, $package);

            if (in_array($data['name'], $ignore)) {
                $ignored[] = $data['name'];

                continue;
            }

            $plugged[] = $data['name'];

            $localConfig['require'][$data['name']] = '*';
            $localConfig['repositories'][] = $this->createRepositoryItem($package);
        }

        if (static::$loaded === false) {
            if ($plugged) {
                $io->write('<info>Plugged packages</info>');
            }

            foreach ($plugged as $package) {
                $io->write("  Plugged: <info>$package</info>");
            }

            if ($ignored) {
                $io->write('<info>Ignored packages</info>');
            }

            foreach ($ignored as $package) {
                $io->write("  Ignored: $package");
            }

            static::$loaded = true;
        }

        if ($fullLoad) {
            $this->saveComposerPlugAndPlayFile($localConfig);
        }

        return parent::createComposer($io, self::FILENAME, $disablePlugins, $cwd, $fullLoad, $disableScripts);
    }
}
