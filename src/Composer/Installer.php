<?php

namespace Dex\Composer\PlugAndPlay\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

class Installer extends LibraryInstaller
{
    public function getInstallPath(PackageInterface $package): string
    {
        if ($this->isNotPlugAndPlayPackage($package)) {
            return parent::getInstallPath($package);
        }

        $this->initializeVendorDir();

        $relPath = substr($package->getDistUrl(), 2);

        return dirname($this->vendorDir) . '/' . $relPath;
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package): PromiseInterface
    {
        if ($this->isNotPlugAndPlayPackage($package)) {
            return parent::install($repo, $package);
        }

        $this->initializeVendorDir();

        $installPath = $this->getInstallPath($package);
        $binaryInstaller = $this->binaryInstaller;

        return resolve(null)->then(static function () use ($repo, $package, $binaryInstaller, $installPath): void {
            $binaryInstaller->installBinaries($package, $installPath);

            if (!$repo->hasPackage($package)) {
                $repo->addPackage(clone $package);
            }
        });
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target): PromiseInterface
    {
        if ($this->isNotPlugAndPlayPackage($target)) {
            return parent::update($repo, $initial, $target);
        }

        $this->initializeVendorDir();

        $installPath = $this->getInstallPath($target);
        $binaryInstaller = $this->binaryInstaller;

        return resolve(null)->then(static function () use ($repo, $initial, $target, $binaryInstaller, $installPath): void {
            $binaryInstaller->removeBinaries($initial);
            $binaryInstaller->installBinaries($target, $installPath);
            $repo->removePackage($initial);

            if (!$repo->hasPackage($target)) {
                $repo->addPackage(clone $target);
            }
        });
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package): PromiseInterface
    {
        if ($this->isNotPlugAndPlayPackage($package)) {
            return parent::uninstall($repo, $package);
        }

        $binaryInstaller = $this->binaryInstaller;

        return resolve(null)->then(static function () use ($repo, $package, $binaryInstaller): void {
            $binaryInstaller->removeBinaries($package);
            $repo->removePackage($package);
        });
    }

    private function isPlugAndPlayPackage(PackageInterface $package): bool
    {
        $distUrl = $package->getDistUrl();

        if (empty($distUrl)) {
            return false;
        }

        return str_starts_with($distUrl, './' . PlugAndPlayInterface::PACKAGES_PATH . '/');
    }

    private function isNotPlugAndPlayPackage(PackageInterface $package): bool
    {
        return !$this->isPlugAndPlayPackage($package);
    }
}
