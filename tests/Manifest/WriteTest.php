<?php

beforeEach()
    ->fixtures('manifest')
    ->prepare();

afterEach()
    ->cleanup();

test('manifest lists the plugged packages and everything they brought along', function () {
    $this->manifest();

    $this->assertManifestEquals([
        'plugged' => [
            'dex/fake',
            'dex/required',
        ],
        'ignored' => [
            'dex/ignore',
        ],
        'installed' => [
            'dex/fake',
            'dex/required',
            'dex/transitive-dependency',
            'dex/transitive-development-dependency',
        ],
        'unplugged' => [],
    ]);
});
