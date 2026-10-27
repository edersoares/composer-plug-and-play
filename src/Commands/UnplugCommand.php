<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Command\BaseCommand;
use Composer\Repository\InstalledRepositoryInterface;
use Dex\Composer\PlugAndPlay\Manifest;
use Dex\Composer\PlugAndPlay\Snapshot;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UnplugCommand extends BaseCommand
{
    use ComposerCreator;
    use CommandNaming;

    protected function configure(): void
    {
        parent::configure();

        $this->naming('plug-and-play:unplug', 'pp:unplug');
        $this->setDescription('Dumps the autoloader as if the plugged packages were not installed');
        $this->addOption('keep', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Plugged package that stays, with its dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputPluginUse($output);

        $composer = $this->requireComposer();
        $installationManager = $composer->getInstallationManager();
        $localRepository = $composer->getRepositoryManager()->getLocalRepository();
        $vendorDir = $composer->getConfig()->get('vendor-dir');

        // Only what nothing that stays needs is hidden: the kept packages, the
        // project's own ones and, recursively, everything they require.
        $candidates = Manifest::installed();
        $staying = array_diff($this->packageNames($localRepository), $candidates);

        $hidden = array_diff(
            $candidates,
            $this->withDependencies($localRepository, array_merge($input->getOption('keep'), $staying))
        );

        Snapshot::save($vendorDir);

        // getPackages() also lists the aliases (dev-main as 9999999-dev), which
        // would keep a package in installed.json after its removal.
        foreach ($localRepository->getPackages() as $package) {
            if (in_array($package->getName(), $hidden, true)) {
                $localRepository->removePackage($package);
            }
        }

        $localRepository->write($localRepository->getDevMode() ?? true, $installationManager);

        $generator = $composer->getAutoloadGenerator();
        $generator->setRunScripts(true);
        $generator->dump($composer->getConfig(), $localRepository, $composer->getPackage(), $installationManager, 'composer');

        Manifest::write();

        foreach (Snapshot::unplugged($vendorDir) as $package) {
            $output->writeln("  Unplugged: <info>$package</info>");
        }

        return 0;
    }

    /**
     * @return string[]
     */
    private function packageNames(InstalledRepositoryInterface $repository): array
    {
        return array_map(fn ($package) => $package->getName(), $repository->getCanonicalPackages());
    }

    /**
     * The given packages and, recursively, the installed packages they require.
     *
     * @param  string[]  $packages
     * @return string[]
     */
    private function withDependencies(InstalledRepositoryInterface $repository, array $packages): array
    {
        $requires = [];

        foreach ($repository->getCanonicalPackages() as $package) {
            $requires[$package->getName()] = array_keys($package->getRequires());
        }

        $kept = [];

        while ($packages) {
            $package = array_shift($packages);

            if (isset($kept[$package])) {
                continue;
            }

            $kept[$package] = true;
            array_push($packages, ...$requires[$package] ?? []);
        }

        return array_keys($kept);
    }
}
