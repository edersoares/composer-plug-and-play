<?php

namespace Dex\Composer\PlugAndPlay\Tests\Composer\Factory;

use Dex\Composer\PlugAndPlay\Tests\FactoryTestCase;

class IgnoredPackagesTest extends FactoryTestCase
{
    protected function fixture(): string
    {
        return 'ignored-packages';
    }

    public function testFactory(): void
    {
        $this->factory();

        $this->assertOutputContains('Plugged: dex/not-ignore');
        $this->assertOutputContains('Ignored: dex/ignore');
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
