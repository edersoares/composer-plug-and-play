<?php

beforeEach()
    ->fixtures('manifest-without-lock')
    ->prepare();

afterEach()
    ->cleanup();

test('without a project lock file only the plugged packages are listed as installed', function () {
    $this->manifest();

    $this->assertManifestEquals([
        'plugged' => [
            'dex/fake',
        ],
        'ignored' => [],
        'installed' => [
            'dex/fake',
        ],
    ]);
});
