<?php

use Dex\Composer\PlugAndPlay\Tests\FactoryTestCase;

uses(FactoryTestCase::class);

beforeEach()
    ->fixture('ignored-packages')
    ->prepare();

afterEach()
    ->cleanup();

test('factory', function () {
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
});
