<?php

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;
use Dex\Composer\PlugAndPlay\Tests\FactoryTestCase;
use Dex\Composer\PlugAndPlay\Tests\ManifestTestCase;

uses(CommandTestCase::class)->in('Commands');
uses(FactoryTestCase::class)->in('Composer/Factory');
uses(ManifestTestCase::class)->in('Manifest');

function fixtures(string $fixture): mixed
{
    return test()->fixtures($fixture);
}

function prepare(): mixed
{
    return test()->prepare();
}

function cleanup(): mixed
{
    return test()->cleanup();
}
