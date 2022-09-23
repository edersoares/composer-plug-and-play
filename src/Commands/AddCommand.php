<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends BaseCommand
{
    use CommandNaming, ComposerCreator;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:add');
        $this->setDescription('Add a required package to install using plug and play plugin.');
        $this->addArgument('package', InputArgument::REQUIRED, 'Package to add in plug and play dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>You are using Composer Plug and Play Plugin.</info>');

        if (file_exists('packages/composer.json') === false) {
            $output->writeln('The [packages/composer.json] file not exists.');

            return 1;
        }

        $composer = file_get_contents('packages/composer.json');

        $json = json_decode($composer, true);

        $json['require'][$input->getArgument('package')] = '*';

        $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        file_put_contents('packages/composer.json', $json);

        return 0;
    }
}
