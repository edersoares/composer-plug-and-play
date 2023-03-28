<?php

namespace Dex\Composer\PlugAndPlay\Tests\Unit\Composer;

use Composer\Composer;
use Composer\IO\BufferIO;
use Composer\IO\IOInterface;
use Dex\Composer\PlugAndPlay\Composer\Factory;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Dex\Composer\PlugAndPlay\Tests\TestCase;

class FactoryTest extends TestCase
{
    const PATH = __DIR__ . '/../../Fixtures/Plugin/';

    private string $cwd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cwd = getcwd();

        chdir(self::PATH);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        chdir($this->cwd);
    }

    public function testFactory(): void
    {
        $this->markTestSkipped();

        $composerPlugAndPlayFile = self::PATH . PlugAndPlayInterface::FILENAME;

        $io = $this->createMock(IOInterface::class);

        $factory = new Factory();

        $composer = $factory->createComposer($io, null, false, self::PATH);

        $json = json_encode([
            'require' => [
                'dex/composer-plug-and-play' => '*',
                'composer/composer' => '*',
                'dex/fake' => '@dev',
            ],
            'config' => [
                'allow-plugins' => true,
            ],
            "extra" => [
                'composer-plug-and-play' => [
                    'ignore' => [
                        "dex/ignore"

                    ]
                ]
            ],
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => '../../../',
                ],
                [
                    'type' => 'path',
                    'url' => './packages/dex/fake',
                    'symlink' => true,
                ],
            ],
            'minimum-stability' => 'dev',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $this->assertInstanceOf(Composer::class, $composer);
        $this->assertFileExists($composerPlugAndPlayFile);
        $this->assertStringEqualsFile($composerPlugAndPlayFile, $json);
    }

    public function testDisplayIgnoredAndPluggedPackages(): void
    {
        $this->markTestSkipped();

        $factory = new Factory();
        $io = new BufferIO();

        $factory->createComposer($io, null, false, self::PATH);

        $output = $io->getOutput();

        $this->assertStringContainsString('Plugged: composer/composer', $output);
        $this->assertStringContainsString('Plugged: dex/fake', $output);
        $this->assertStringContainsString('Ignored: dex/ignore', $output);
    }
}
