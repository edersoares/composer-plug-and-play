<?php

use Dex\Composer\PlugAndPlay\PlugAndPlayInterface;
use Dex\Composer\PlugAndPlay\Snapshot;

beforeEach()
    ->fixtures('manifest')
    ->prepare();

afterEach()
    ->cleanup();

test('packages hidden from a missing installed repository are all unplugged', function () {
    file_put_contents(PlugAndPlayInterface::SNAPSHOT . '.json', json_encode(['packages' => [['name' => 'dex/fake']]]));

    expect(Snapshot::unplugged('vendor'))->toBe(['dex/fake']);
});
