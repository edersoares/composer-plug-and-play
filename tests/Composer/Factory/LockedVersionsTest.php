<?php

namespace Dex\Composer\PlugAndPlay\Tests\Composer\Factory;

use Dex\Composer\PlugAndPlay\Tests\FactoryTestCase;

class LockedVersionsTest extends FactoryTestCase
{
    protected function fixture(): string
    {
        return 'locked-versions';
    }

    public function testFactory(): void
    {
        $this->factory();

        $this->assertGeneratedJsonEquals([
            'require' => [
                'composer/composer' => '2.5.0',
                'composer/ca-bundle' => '1.3.5',
                'composer/class-map-generator' => '1.0.0',
                'composer/metadata-minifier' => '1.0.0',
                'composer/pcre' => '3.1.0',
                'composer/semver' => '3.3.2',
                'composer/spdx-licenses' => '1.5.7',
                'composer/xdebug-handler' => '3.0.3',
                'justinrainbow/json-schema' => '5.2.12',
                'psr/container' => '2.0.2',
                'psr/log' => '3.0.0',
                'react/promise' => 'v2.9.0',
                'seld/jsonlint' => '1.9.0',
                'seld/phar-utils' => '1.2.1',
                'seld/signal-handler' => '2.0.1',
                'symfony/console' => 'v6.2.7',
                'symfony/deprecation-contracts' => 'v3.2.1',
                'symfony/filesystem' => 'v6.2.7',
                'symfony/finder' => 'v6.2.7',
                'symfony/polyfill-ctype' => 'v1.27.0',
                'symfony/polyfill-intl-grapheme' => 'v1.27.0',
                'symfony/polyfill-intl-normalizer' => 'v1.27.0',
                'symfony/polyfill-mbstring' => 'v1.27.0',
                'symfony/polyfill-php73' => 'v1.27.0',
                'symfony/polyfill-php80' => 'v1.27.0',
                'symfony/polyfill-php81' => 'v1.27.0',
                'symfony/process' => 'v6.2.7',
                'symfony/service-contracts' => 'v3.2.1',
                'symfony/string' => 'v6.2.7',
            ],
            'config' => [
                'allow-plugins' => true,
            ],
        ]);
    }
}
