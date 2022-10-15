<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Composer;
use Composer\Console\Application;
use Composer\Json\JsonValidationException;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ComposerCreator
{
    protected bool $usePlugAndPlay = false;

    /**
     * @param bool      $required
     * @param bool|null $disablePlugins
     * @param bool|null $disableScripts
     *
     * @throws JsonValidationException
     * @return Composer|null
     */
    public function getComposer(bool $required = true, ?bool $disablePlugins = null, ?bool $disableScripts = null): ?Composer
    {
        $composer = $this->composer ?? null;

        if (is_null($composer)) {
            $composer = parent::getComposer($required, $disablePlugins, $disableScripts);
        }

        if ($required) {
            $this->composer = $composer;
        }

        return $composer;
    }

    /**
     * Retrieves the default Composer\Composer instance or throws
     *
     * Use this instead of getComposer if you absolutely need an instance
     *
     * @see Application::getPluginCommands()
     *
     * @param bool|null $disablePlugins If null, reads --no-plugins as default
     * @param bool|null $disableScripts If null, reads --no-scripts as default
     *
     * @throws \RuntimeException
     */
    public function requireComposer(?bool $disablePlugins = null, ?bool $disableScripts = null): Composer
    {
        // It's needed that Composer will be reset because
        // Application::getPluginCommands() creates a Composer instance without
        // plug and play capabilities.

        $this->resetComposer();

        return Factory::create(
            $this->getApplication()->getIO(),
            disablePlugins: boolval($disablePlugins),
            disableScripts: boolval($disableScripts)
        );
    }

    /**
     * Check if plug and play plugin is running.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isTheCommand = $input->getFirstArgument() === $this->getName();

        if ($isTheCommand) {
            $this->usePlugAndPlay = true;
            $output->writeln('<info>You are using Composer Plug and Play Plugin.</info>');
        }

        if ($input->getOption('plug-and-play-pretend')) {
            return 0;
        }

        return parent::execute($input, $output);
    }
}
