<?php

namespace Dex\Composer\PlugAndPlay\Tests\Composer\Factory;

use Dex\Composer\PlugAndPlay\Tests\FactoryTestCase;

class CustomRepositoriesTest extends FactoryTestCase
{
    protected function path(): string
    {
        return __DIR__ . '/../../../fixtures/custom-repositories/';
    }

    public function testFactory(): void
    {
        $this->factory();

        $this->assertOutput('Plugged: dex/fake');
        $this->assertGeneratedJsonEquals([
            'config' => [
                'allow-plugins' => true,
            ],
            'require' => [
                'dex/fake' => '*',
            ],
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => '../packages/dex/fake',
                ],
            ],
        ]);
    }
}
