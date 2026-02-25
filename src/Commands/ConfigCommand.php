<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\ConfigCommand as BaseCommand;
use Composer\Config;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigCommand extends BaseCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:config', 'pp:config');
        $this->setDescription('Sets config options in packages/composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputPluginUse($output);

        $exit = parent::execute($input, $output);

        $file = file_get_contents(PlugAndPlayInterface::PACKAGES_FILE);

        $data = json_decode($file, true);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        file_put_contents(PlugAndPlayInterface::PACKAGES_FILE, $json);

        return $exit;
    }

    protected function getComposerConfigFile(InputInterface $input, Config $config): string
    {
        return PlugAndPlayInterface::PACKAGES_FILE;
    }
}
