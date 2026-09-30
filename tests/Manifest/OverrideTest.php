<?php

beforeEach()
    ->fixtures('manifest-override')
    ->prepare();

afterEach()
    ->cleanup();

test('plugged package that overrides a locked dependency is not listed as installed', function () {
    $this->manifest();

    $this->assertManifestEquals([
        'plugged' => [
            'dex/fake',
        ],
        'ignored' => [],
        'installed' => [
            'dex/transitive-dependency',
        ],
    ]);
});
