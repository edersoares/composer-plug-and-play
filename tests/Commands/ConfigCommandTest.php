<?php

beforeEach()
    ->fixtures('config-command')
    ->prepare();

afterEach()
    ->cleanup();

test('config command', function () {
    $this->runCommand('plug-and-play:config extra.composer-plug-and-play.autoload-dev --json \'["dex/fake"]\' --merge');

    $this->assertOutputContains('You are using Composer Plug and Play Plugin.');
    $this->assertPackagesFileJsonEquals([
        'extra' => [
            'composer-plug-and-play' => [
                'autoload-dev' => [
                    'dex/fake',
                ],
            ],
        ],
    ]);
});
