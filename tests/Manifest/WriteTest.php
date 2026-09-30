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
        ],
        'ignored' => [
            'dex/ignore',
        ],
        'installed' => [
            'dex/fake',
            'dex/transitive-dependency',
            'dex/transitive-development-dependency',
        ],
    ]);
});
