<?php

use Dex\Composer\PlugAndPlay\Tests\CommandTestCase;
use Dex\Composer\PlugAndPlay\Tests\FactoryTestCase;

uses(CommandTestCase::class)->in('Commands');
uses(FactoryTestCase::class)->in('Composer/Factory');

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
