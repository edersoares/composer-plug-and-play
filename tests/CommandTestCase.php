<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class CommandTestCase extends TestCase
{
    protected string $directory = __DIR__ . '/../../tmp/';

    protected function runCommand(string $command): BufferedOutput
    {
        $application = new Application();
        $input = new StringInput("$command -d $this->directory");
        $output = new BufferedOutput();

        $application->doRun($input, $output);

        return $output;
    }

    protected function encodeContent(array $data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    protected function packagesFile(): string
    {
        return $this->directory . PlugAndPlayInterface::PACKAGES_FILE;
    }
}
