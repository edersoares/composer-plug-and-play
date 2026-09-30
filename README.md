# Composer Plug and Play

<a href="https://packagist.org/packages/dex/composer-plug-and-play"><img src="https://img.shields.io/packagist/v/dex/composer-plug-and-play" alt="Packagist Version" /></a>
<a href="https://packagist.org/packages/dex/composer-plug-and-play"><img src="https://img.shields.io/packagist/php-v/dex/composer-plug-and-play" alt="PHP Version" /></a>
<a href="https://packagist.org/packages/dex/composer-plug-and-play"><img src="https://img.shields.io/packagist/dt/dex/composer-plug-and-play" alt="Downloads" /></a>
<a href="https://github.com/edersoares/composer-plug-and-play/blob/main/LICENSE.md"><img src="https://img.shields.io/github/license/edersoares/composer-plug-and-play" alt="License" /></a>
<a href="https://github.com/edersoares/composer-plug-and-play/actions"><img src="https://img.shields.io/github/actions/workflow/status/edersoares/composer-plug-and-play/tests.yml?branch=main&label=tests" alt="Tests" /></a>

Composer Plug and Play lets you develop local packages inside a project without modifying `composer.json`. Keep your
working packages in `packages/`, run `composer plug-and-play`, and get a fully resolved Composer environment — without
polluting your real lock file.

## Table of Contents

- [Why?](#why)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Directories and Files](#directories-and-files)
- [Commands](#commands)
- [Configuration](#configuration)
- [The Manifest File](#the-manifest-file)
- [Contributing](#contributing)
- [License](#license)

## Why?

Working with local packages via `path` repositories in `composer.json` pollutes the project's `composer.json` and
`composer.lock` with development-only entries that must not be committed.

Composer Plug and Play keeps this configuration isolated in `packages/composer.json` and `packages/plug-and-play.lock`,
so your real `composer.json` stays clean.

## Requirements

- PHP 8.3 or higher
- Composer 2.3.0 or higher

## Installation

```bash
composer require dex/composer-plug-and-play
```

### Global installation

You can install [Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) globally to use its
abilities in all your local projects.

```bash
composer global require dex/composer-plug-and-play
```

## Quick Start

1. Initialize the plug-and-play structure in your project:

   ```bash
   composer plug-and-play:init
   ```

2. Clone or create a package inside `packages/`:

   ```bash
   git clone git@github.com:your-org/your-package.git packages/your-org/your-package
   ```

3. Run plug-and-play to resolve everything:

   ```bash
   composer plug-and-play
   ```

Your package is now available as if installed via Composer, without touching `composer.json`.

## Directories and Files

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) plugin needs a `packages` folder in
the project root directory where the plug and play structure will live.

```
packages/                           ← managed by plug-and-play
├── <vendor>/<package>/             ← your local package (cloned or created)
│   ├── composer.json
│   └── ...
├── composer.json                   ← your plug-and-play configuration
├── plug-and-play.json              ← generated merged config (do not commit)
└── plug-and-play.lock              ← generated lock file (do not commit)

composer.json                       ← your real project config (unchanged)
composer.lock                       ← your real lock file (unchanged)
vendor/                             ← standard Composer vendor directory
```

## Commands

All commands use the `plug-and-play.json` and `plug-and-play.lock` files as source to project dependencies instead of
`composer.json` and `composer.lock` original files.

You can use `composer pp` and `composer pp:*` as alias for all commands.

| Command                 | Description                                                            |
|-------------------------|------------------------------------------------------------------------|
| `plug-and-play`         | Installs plug and play dependencies together with project dependencies |
| `plug-and-play:add`     | Require a package into `packages/composer.json`                        |
| `plug-and-play:dump`    | Same as `composer dump-autoload`, but using `plug-and-play` files      |
| `plug-and-play:init`    | Initialize plug and play plugin                                        |
| `plug-and-play:install` | Same as `composer install`, but using `plug-and-play` files            |
| `plug-and-play:reset`   | Remove `plug-and-play` files                                           |
| `plug-and-play:run`     | Same as `composer run-script`, but using `plug-and-play` files         |
| `plug-and-play:update`  | Same as `composer update`, but using `plug-and-play` files             |

## Configuration

You can add additional configuration in `packages/composer.json` under the `extra.composer-plug-and-play` key.

### Ignore plugged packages

Sometimes you may need to ignore a package that is under development:

```json
{
    "extra": {
        "composer-plug-and-play": {
            "ignore": [
                "vendor-name/package-to-ignore"
            ]
        }
    }
}
```

### Require dev dependencies from plugged packages

When developing a package or library you may need to require its dev dependencies:

```json
{
    "extra": {
        "composer-plug-and-play": {
            "require-dev": [
                "vendor-name/package-to-require-dev"
            ]
        }
    }
}
```

### Autoload dev dependencies from plugged packages

When developing a package or library you may need to autoload its dev dependencies:

```json
{
    "extra": {
        "composer-plug-and-play": {
            "autoload-dev": [
                "vendor-name/package-to-autoload-dev"
            ]
        }
    }
}
```

## The Manifest File

Every install, update and autoload dump writes `packages/plug-and-play.php`, a plain PHP file your application can
`require` to find out which packages are only there because of plug and play.

```php
<?php return [
    'plugged' => ['acme/blog'],
    'ignored' => ['acme/draft'],
    'installed' => ['acme/blog', 'league/commonmark'],
];
```

- `plugged` — the packages found under `packages/<vendor>/<package>`.
- `ignored` — the plugged packages listed in `extra.composer-plug-and-play.ignore`.
- `installed` — `plugged` plus every dependency they dragged in, computed as the difference between
  `packages/plug-and-play.lock` and your real `composer.lock`. **This is the list you want**: it is what would
  disappear from `vendor` if you ran `plug-and-play:reset`.

`plug-and-play:reset` deletes the file, so its absence simply means nothing is plugged.

### Running a Laravel test suite as if nothing were plugged

A plugged package's service provider is auto-discovered, so it registers routes, config, observers and migrations
into the host application — which can break the host's own test suite. `installed` lets you switch that off at
runtime, without uninstalling anything.

Filter Laravel's `PackageManifest`, which is what feeds both `providers()` and `aliases()`:

```php
namespace App\Support;

use Illuminate\Foundation\PackageManifest;

class UnpluggedPackageManifest extends PackageManifest
{
    protected function getManifest()
    {
        $file = $this->basePath . '/packages/plug-and-play.php';

        $plugged = is_file($file) ? (require $file)['installed'] ?? [] : [];

        return array_diff_key(parent::getManifest(), array_flip($plugged));
    }
}
```

Then bind it in `bootstrap/app.php` when a flag asks for it, and set that flag in `phpunit.xml`:

```php
$app->beforeBootstrapping(Illuminate\Foundation\Bootstrap\RegisterProviders::class, function ($app) {
    if (env('PLUG_AND_PLAY', true)) {
        return;
    }

    $app->singleton(PackageManifest::class, fn () => new UnpluggedPackageManifest(
        new Illuminate\Filesystem\Filesystem, $app->basePath(), $app->getCachedPackagesPath()
    ));
});
```

Because the packages' service providers are never registered, their `loadMigrationsFrom()` calls never run either —
so `php artisan migrate` stops seeing their migrations too.

## Contributing

Contributions are welcome. Please open an issue before submitting a pull request so the change can be discussed first.

```bash
composer test     # run all tests
composer format   # format code with Laravel Pint (PSR-12)
```

## License

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) is licensed under the MIT license.
See the [license](https://github.com/edersoares/composer-plug-and-play/blob/main/LICENSE.md) file for more details.
