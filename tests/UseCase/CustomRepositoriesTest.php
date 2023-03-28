<?php

namespace Dex\Composer\PlugAndPlay\Tests\UseCase;

use Dex\Composer\PlugAndPlay\Tests\UseCase;

class CustomRepositoriesTest extends UseCase
{
    protected function path(): string
    {
        return __DIR__ . '/../../fixtures/custom-repositories/';
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
/*

            "type": "path",
            "url": "../packages/dex/fake",
            "options": {
                "reference": "none",
                "versions": {
                    "dex/fake": "0.0.0"
                }
            }
*/
