<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlugAndPlayCommand extends BaseCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play', 'pp');
        $this->setDescription('Installs plug and play dependencies together project dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputPluginUse($output);

        $locker = $this->requireComposer()->getLocker();
        $runInstall = $locker->isLocked() && $locker->isFresh();

        $command = $runInstall
            ? new InstallCommand()
            : new UpdateCommand();

        $command->setApplication($this->getApplication());
        $command->initialize($input, $output);

        return $command->run($input, $output);
    }
}
