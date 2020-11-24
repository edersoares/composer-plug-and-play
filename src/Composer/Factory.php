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
     * Saves composer-plug-and-play.json and composer-plug-and-play.lock files.
     *
     * @param array $data
     *
     * @return void
     */
    private function saveComposerPlugAndPlayFile(array $data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

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
            'url' => realpath(dirname($package)),
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
        $fullLoad = true
    ) {
        $cwd = $cwd ?: getcwd();

        if (null === $localConfig) {
            $localConfig = static::getComposerFile();
        }

        if (is_string($localConfig)) {
            $localConfig = $this->loadJsonFile($io, $localConfig);
        }

        $packages = glob(self::PATH);

        foreach ($packages as $package) {
            $data = $this->loadJsonFile($io, $package);

            $localConfig['require'][$data['name']] = '*';
            $localConfig['repositories'][] = $this->createRepositoryItem($package);
        }

        if ($fullLoad) {
            $this->saveComposerPlugAndPlayFile($localConfig);
        }

        return parent::createComposer($io, self::FILENAME, $disablePlugins, $cwd, $fullLoad);
    }
}
