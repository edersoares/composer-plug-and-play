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
     * @var Composer
     */
    protected $composer;

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
        $composer = $this->composer;

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
