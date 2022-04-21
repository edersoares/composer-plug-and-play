<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlugAndPlayCommand extends BaseCommand
{
    use CommandNaming, ComposerCreator;

    protected function configure()
    {
        parent::configure();

        $this->naming('plug-and-play');
        $this->setDescription('Installs plug and play dependencies together project dependencies.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>You are using Composer Plug and Play Plugin.</info>');

        if ($input->getOption('plug-and-play-pretend')) {
            return 0;
        }

        $locker = $this->getComposer()->getLocker();
        $runInstall = $locker->isLocked() && $locker->isFresh();

        $command = $runInstall
            ? new InstallCommand()
            : new UpdateCommand();

        $command->setApplication($this->getApplication());
        $command->initialize($input, $output);

        return $command->run($input, $output);
    }
}
