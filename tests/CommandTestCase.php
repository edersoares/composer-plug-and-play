<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class CommandTestCase extends TestCase
{
    use TestConcerns;

    protected function runCommand(string $command): void
    {
        $application = new Application();
        $input = new StringInput($command);
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        $this->output = $output->fetch();
    }
}
