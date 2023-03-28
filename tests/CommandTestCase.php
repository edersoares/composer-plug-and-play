<?php

namespace Dex\Composer\PlugAndPlay\Tests;

use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class CommandTestCase extends TestCase
{
    protected string $cwd;
    protected BufferedOutput $output;

    abstract protected function fixture(): string;

    protected function path(): string
    {
        return __DIR__ . '/../tmp/';
    }

    protected function fixturesPath(): string
    {
        return __DIR__ . '/../fixtures/' . $this->fixture();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->cwd = getcwd();
        $this->output = new BufferedOutput();

        $from = $this->fixturesPath();
        $tmp = $this->path() . $this->fixture();

        exec("cp -R $from $tmp");

        chdir($tmp);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $tmp = $this->path() . $this->fixture();

        exec("rm -r $tmp");

        chdir($this->cwd);
    }

    protected function getPackagesFile(): string
    {
        return $this->path() . $this->fixture() . '/' . PlugAndPlayInterface::PACKAGES_FILE;
    }

    protected function runCommand(string $command): void
    {
        $application = new Application();
        $input = new StringInput($command);

        $application->doRun($input, $this->output);
    }

    protected function assertPackagesFileJsonEquals(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $this->assertStringEqualsFile($this->getPackagesFile(), $json);
    }

    protected function assertOutputContains(string $string): void
    {
        $this->assertStringContainsString($string, $this->output->fetch());
    }
}
