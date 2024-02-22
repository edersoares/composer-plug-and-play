<?php

beforeEach()
    ->fixture('locked-versions')
    ->prepare();

afterEach()
    ->cleanup();

test('factory', function () {
    $this->factory();

    $this->assertGeneratedJsonEquals([
        'require' => [
            'composer/composer' => '2.5.0',
            'composer/ca-bundle' => '1.4.0',
            'composer/class-map-generator' => '1.1.0',
            'composer/metadata-minifier' => '1.0.0',
            'composer/pcre' => '3.1.1',
            'composer/semver' => '3.4.0',
            'composer/spdx-licenses' => '1.5.8',
            'composer/xdebug-handler' => '3.0.3',
            'justinrainbow/json-schema' => 'v5.2.13',
            'psr/container' => '2.0.2',
            'psr/log' => '3.0.0',
            'react/promise' => 'v2.11.0',
            'seld/jsonlint' => '1.10.2',
            'seld/phar-utils' => '1.2.1',
            'seld/signal-handler' => '2.0.2',
            'symfony/console' => 'v6.4.3',
            'symfony/deprecation-contracts' => 'v3.4.0',
            'symfony/filesystem' => 'v6.4.3',
            'symfony/finder' => 'v6.4.0',
            'symfony/polyfill-ctype' => 'v1.29.0',
            'symfony/polyfill-intl-grapheme' => 'v1.29.0',
            'symfony/polyfill-intl-normalizer' => 'v1.29.0',
            'symfony/polyfill-mbstring' => 'v1.29.0',
            'symfony/polyfill-php73' => 'v1.29.0',
            'symfony/polyfill-php80' => 'v1.29.0',
            'symfony/polyfill-php81' => 'v1.29.0',
            'symfony/process' => 'v6.4.3',
            'symfony/service-contracts' => 'v3.4.1',
            'symfony/string' => 'v7.0.3',
        ],
        'require-dev' => [
            'dex/composer-plug-and-play' => '0.21.0',
        ],
        'config' => [
            'allow-plugins' => true,
        ],
    ]);
});
