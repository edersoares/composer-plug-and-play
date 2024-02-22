<?php

beforeEach()
    ->fixture('merge-keys')
    ->prepare();

afterEach()
    ->cleanup();

test('merge `composer.json` keys', function () {
    $this->factory();

    $this->assertGeneratedJsonEquals([
        "minimum-stability" => "dev",
        "prefer-stable" => false,
        'config' => [
            'allow-plugins' => [
                'dex/composer-plug-and-play' => true,
            ],
        ],
    ]);
});
