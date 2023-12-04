<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends BaseCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:init', 'pp:init');
        $this->setDescription('Initialize plug and play plugin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputPluginUse($output);

        if (is_dir('packages')) {
            $output->writeln('The [packages] directory already exists.');
        } else {
            mkdir('packages');
        }

        if (file_exists('packages/.gitignore')) {
            $output->writeln('The [packages/.gitignore] file already exists.');
        } else {
            file_put_contents('packages/.gitignore', $this->gitignore());
        }

        if (file_exists('packages/composer.json')) {
            $output->writeln('The [packages/composer.json] file already exists.');
        } else {
            file_put_contents('packages/composer.json', $this->composer());
        }

        return 0;
    }

    private function gitignore(): string
    {
        return <<<IGNORE
*
*/
.gitignore
IGNORE;
    }

    private function composer(): string
    {
        return <<<COMPOSER
{
    "config": {
        "allow-plugins": {
            "dex/composer-plug-and-play": true
        }
    },
    "extra": {
        "composer-plug-and-play": {
            "ignore": []
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
COMPOSER;
    }
}
