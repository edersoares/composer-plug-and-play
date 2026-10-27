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

test('a package found in more than one directory comes from the first one', function () {
    mkdir('libraries/dex/from-packages', recursive: true);
    file_put_contents('libraries/dex/from-packages/composer.json', '{"name": "dex/from-packages"}');

    $this->factory();

    $repositories = json_decode(file_get_contents('packages/plug-and-play.json'), true)['repositories'];

    expect(array_column($repositories, 'url'))->toBe([
        './packages/dex/from-packages',
        './libraries/dex/from-libraries',
    ]);
});

test('directories may be listed in the project composer.json', function () {
    file_put_contents('composer.json', json_encode([
        'config' => ['allow-plugins' => true],
        'extra' => ['composer-plug-and-play' => ['directories' => ['libraries']]],
    ]));
    file_put_contents('packages/composer.json', '{}');

    $this->factory();

    $this->assertOutputContains('Plugged: dex/from-libraries');
    $this->assertOutputContains('Plugged: dex/from-packages');
});
