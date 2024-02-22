<?php

beforeEach()
    ->fixture('custom-repositories')
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
            'dex/fake' => '*',
        ],
        'repositories' => [
            [
                'type' => 'path',
                'url' => '../packages/dex/fake',
            ],
        ],
    ]);
});
