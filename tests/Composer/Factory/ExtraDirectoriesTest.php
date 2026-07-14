<?php

beforeEach()
    ->fixtures('extra-directories')
    ->prepare();

afterEach()
    ->cleanup();

test('factory', function () {
    $this->factory();

    $this->assertOutputContains('Plugged: dex/from-libraries');
    $this->assertOutputContains('Plugged: dex/from-packages');
    $this->assertGeneratedJsonEquals([
        'config' => [
            'allow-plugins' => true,
        ],
        'extra' => [
            'composer-plug-and-play' => [
                'directories' => [
                    'libraries',
                ],
            ],
        ],
        'require' => [
            'dex/from-packages' => '@dev',
            'dex/from-libraries' => '@dev',
        ],
        'repositories' => [
            [
                'type' => 'path',
                'url' => './packages/dex/from-packages',
                'symlink' => true,
            ],
            [
                'type' => 'path',
                'url' => './libraries/dex/from-libraries',
                'symlink' => true,
            ],
        ],
    ]);
});
