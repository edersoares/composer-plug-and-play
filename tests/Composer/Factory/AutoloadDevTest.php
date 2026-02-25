<?php

beforeEach()
    ->fixtures('autoload-dev')
    ->prepare();

afterEach()
    ->cleanup();

test('factory', function () {
    $this->factory();

    $this->assertGeneratedJsonEquals([
        'config' => [
            'allow-plugins' => true,
        ],
        'extra' => [
            'composer-plug-and-play' => [
                'autoload-dev' => ['dex/fake'],
            ],
        ],
        'autoload-dev' => [
            'psr-4' => [
                'Dex\\Fake\\Tests\\' => 'packages/dex/fake/tests/',
            ],
        ],
        'require' => [
            'dex/fake' => '@dev',
        ],
        'repositories' => [
            [
                'type' => 'path',
                'url' => './packages/dex/fake',
                'symlink' => true,
            ],
        ],
    ]);
});
