<?php

beforeEach()
    ->fixture('require-dev')
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
                'require-dev' => [
                    'dex/fake',
                ],
            ]
        ],
        'require-dev' => [
            'dex/composer-plug-and-play' => '@dev',
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
