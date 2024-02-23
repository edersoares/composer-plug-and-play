<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\BaseCommand;
use Composer\Util\Filesystem;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetCommand extends BaseCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:reset', 'pp:reset');
        $this->setDescription('Reset plug and play files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputPluginUse($output);

        $filesystem = new Filesystem();

        $filesystem->removeDirectoryPhp(PlugAndPlayInterface::PACKAGES_VENDOR);

        if (file_exists(PlugAndPlayInterface::FILENAME)) {
            $filesystem->unlink(PlugAndPlayInterface::FILENAME);
        }

        $lock = str_replace('.json', '.lock', PlugAndPlayInterface::FILENAME);

        if (file_exists($lock)) {
            $filesystem->unlink($lock);
        }

        return 0;
    }
}
