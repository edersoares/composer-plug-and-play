<?php

function fixture(string $fixture): mixed
{
    return test()->fixture($fixture);
}

function prepare(): mixed
{
    return test()->prepare();
}

function cleanup(): mixed
{
    return test()->cleanup();
}