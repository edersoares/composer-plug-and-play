<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Composer;
use Composer\Console\Application;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ComposerCreator
{
    /**
     * Retrieves the default Composer\Composer instance or throws
     *
     * Use this instead of getComposer if you absolutely need an instance
     *
     * @see Application::getPluginCommands()
     *
     * @throws RuntimeException
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isTheCommand = $input->getFirstArgument() === $this->getName();

        if ($isTheCommand) {
            $this->outputPluginUse($output);
        }

        return parent::execute($input, $output);
    }

    public function outputPluginUse(OutputInterface $output): void
    {
        $output->writeln('<info>You are using Composer Plug and Play Plugin.</info>');
    }
}
