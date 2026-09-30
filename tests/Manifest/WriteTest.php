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
            'dex/remote',
        ],
        'ignored' => [
            'dex/ignore',
        ],
        'installed' => [
            'dex/fake',
            'dex/remote',
            'dex/transitive-dependency',
            'dex/transitive-development-dependency',
        ],
    ]);
});

test('manifest is not written without a plug and play lock file', function () {
    unlink('packages/plug-and-play.lock');

    $this->manifest();

    $this->assertFileDoesNotExist($this->path() . $this->fixture . '/packages/plug-and-play.php');
});

test('manifest is not written when the project has no packages directory', function () {
    exec('rm -r packages');

    $this->manifest();

    $this->assertFileDoesNotExist($this->path() . $this->fixture . '/packages/plug-and-play.php');
});
