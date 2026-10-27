<?php

beforeEach()
    ->fixtures('extra-directories')
    ->prepare();

afterEach()
    ->cleanup();

test('manifest lists the packages of every directory and ignores them as well', function () {
    mkdir('libraries/dex/draft', recursive: true);
    file_put_contents('libraries/dex/draft/composer.json', '{"name": "dex/draft"}');
    file_put_contents('packages/composer.json', json_encode([
        'extra' => ['composer-plug-and-play' => ['directories' => ['libraries'], 'ignore' => ['dex/draft']]],
    ]));

    $this->manifest();

    $this->assertManifestEquals([
        'plugged' => ['dex/from-libraries', 'dex/from-packages'],
        'ignored' => ['dex/draft'],
        'installed' => ['dex/from-libraries', 'dex/from-packages'],
        'unplugged' => [],
    ]);
});
