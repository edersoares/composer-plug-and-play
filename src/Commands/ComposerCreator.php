<?php

namespace Dex\Composer\PlugAndPlay\Commands;

use Composer\Composer;
use Composer\Console\Application;
use Composer\Json\JsonValidationException;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ComposerCreator
{
    /**
     * @var bool
     */
    protected $usePlugAndPlay = false;

    /**
     * @param bool      $required
     * @param bool|null $disablePlugins
     *
     * @throws JsonValidationException|RuntimeException
     *
     * @return Composer
     */
    public function getComposer($required = true, $disablePlugins = null, $disableScripts = null)
    {
        $composer = $this->composer ?? null;

        if ($this->usePlugAndPlay && is_null($composer)) {
            $application = $this->getApplication();

            if ($application instanceof Application) {
                $composer = Factory::create($application->getIO(), null, $disablePlugins);
            } elseif ($required) {
                throw new RuntimeException(
                    'Could not create a Composer\Composer instance, you must inject ' .
                    'one if this command is not used with a Composer\Console\Application instance'
                );
            }
        }

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
     * @throws \RuntimeException
     */
    public function requireComposer(bool $disablePlugins = null, bool $disableScripts = null): Composer
    {
        // It's needed that Composer will be reseted because
        // Application::getPluginCommands() creates a Composer instance without
        // plug and play capabilities.

        $this->resetComposer();

        return Factory::create($this->getApplication()->getIO());
    }

    /**
     * Check if plug and play plugin is running.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
