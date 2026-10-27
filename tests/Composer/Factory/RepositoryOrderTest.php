<?php

beforeEach()
    ->fixtures('repository-order')
    ->prepare();

afterEach()
    ->cleanup();

test('factory', function () {
    $this->factory();

    $this->assertOutputContains('Plugged: dex/fake');
    $this->assertGeneratedJsonEquals([
        'config' => [
            'allow-plugins' => true,
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
            [
                'type' => 'vcs',
                'url' => 'https://example.com/original-repo',
            ],
        ],
    ]);
});
