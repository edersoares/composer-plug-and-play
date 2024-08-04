<?php

beforeEach()
    ->fixture('autoload-strategy')
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
                'strategy' => 'experimental:autoload',
                'autoload-dev' => ['dex/fake'],
            ],
        ],
        'autoload' => [
            'psr-4' => [
                'Dex\\Fake\\' => 'packages/dex/fake/src/',
            ],
            'files' => [
                'packages/dex/fake/scripts/start.php',
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
                'url' => './packages/vendor/dex/fake',
                'symlink' => true,
            ],
        ],
    ]);
});
