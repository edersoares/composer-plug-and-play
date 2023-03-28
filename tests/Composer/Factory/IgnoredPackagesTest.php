<?php

namespace Dex\Composer\PlugAndPlay\Tests\Composer\Factory;

use Dex\Composer\PlugAndPlay\Tests\FactoryTestCase;

class IgnoredPackagesTest extends FactoryTestCase
{
    protected function path(): string
    {
        return __DIR__ . '/../../../fixtures/ignored-packages/';
    }

    public function testFactory(): void
    {
        $this->factory();

        $this->assertOutput('Plugged: dex/not-ignore');
        $this->assertOutput('Ignored: dex/ignore');
        $this->assertGeneratedJsonEquals([
            'extra' => [
                'composer-plug-and-play' => [
                    'ignore' => [
                        'dex/ignore',
                    ],
                ],
            ],
            'config' => [
                'allow-plugins' => true,
            ],
            'require' => [
                'dex/not-ignore' => '@dev',
            ],
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => './packages/dex/not-ignore',
                    'symlink' => true,
                ],
            ],
        ]);
    }
}
