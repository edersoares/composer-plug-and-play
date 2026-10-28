<?php

use Composer\Composer;
use Composer\IO\NullIO;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Dex\Composer\PlugAndPlay\PlugAndPlayPlugin;

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

test('manifest is not written without the packages directory', function () {
    exec('rm -r ' . PlugAndPlayInterface::PACKAGES_PATH);

    $this->manifest();

    expect(PlugAndPlayInterface::MANIFEST)->not->toBeFile();
});

test('manifest skips packages without a name', function () {
    mkdir('packages/dex/nameless');
    file_put_contents('packages/dex/nameless/composer.json', '{}');

    $this->manifest();

    expect((require PlugAndPlayInterface::MANIFEST)['plugged'])->toBe(['dex/fake', 'dex/required']); // NOSONAR
});

test('plugin writes the manifest', function () {
    $event = new Event(ScriptEvents::POST_AUTOLOAD_DUMP, new Composer(), new NullIO());

    (new PlugAndPlayPlugin())->writeManifest($event);

    expect(PlugAndPlayInterface::MANIFEST)->toBeFile();
});
