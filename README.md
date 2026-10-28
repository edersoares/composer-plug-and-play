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
- [How It Works](#how-it-works)
- [Directories and Files](#directories-and-files)
- [Commands](#commands)
- [Configuration](#configuration)
- [Recipes](#recipes)
- [Unplugging Packages](#unplugging-packages)
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
abilities in all your local projects, without adding it to any of them:

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

Your package is now available as if installed via Composer, without touching `composer.json`. The output tells you
what was plugged:

```
You are using Composer Plug and Play Plugin.
Plugged packages
  Plugged: your-org/your-package
```

Edit the package in `packages/your-org/your-package` and the project sees the changes right away: it is used from
where it is, not copied to `vendor`.

To go back to the project exactly as it is committed:

```bash
composer plug-and-play:reset
composer install
```

## How It Works

Every plug and play command builds `packages/plug-and-play.json` and runs Composer with it instead of your
`composer.json`:

1. It starts from your `composer.json`, with every package locked in your `composer.lock` pinned to its locked
   version, so plugging a package does not upgrade the rest of the project.
2. It merges `packages/composer.json` on top of it: its `require`, `repositories`, `scripts` and other keys are added,
   and its `minimum-stability`, `prefer-stable` and `config.allow-plugins` win.
3. Each `packages/<vendor>/<package>/composer.json` becomes a `path` repository required as `@dev`, replacing the
   version your project requires, if any.

Composer then installs and locks against `packages/plug-and-play.json` and `packages/plug-and-play.lock`, so your
`composer.json` and `composer.lock` are never written.

## Directories and Files

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) plugin needs a `packages` folder in
the project root directory where the plug and play structure will live.

```
packages/                           ← managed by plug-and-play
├── <vendor>/<package>/             ← your local package (cloned or created)
│   ├── composer.json
│   └── ...
├── .gitignore                      ← created by plug-and-play:init, ignores the whole folder
├── composer.json                   ← your plug-and-play configuration
├── plug-and-play.json              ← generated merged config
├── plug-and-play.lock              ← generated lock file
├── plug-and-play.php               ← generated manifest, see "The Manifest File"
└── plug-and-play.installed.*       ← kept only while plug-and-play:unplug is active

composer.json                       ← your real project config (unchanged)
composer.lock                       ← your real lock file (unchanged)
vendor/                             ← standard Composer vendor directory
```

Nothing under `packages/` is meant to be committed: the `.gitignore` created by `plug-and-play:init` keeps the whole
folder, itself included, out of Git.

## Commands

All commands use the `plug-and-play.json` and `plug-and-play.lock` files as source to project dependencies instead of
`composer.json` and `composer.lock` original files.

You can use `composer pp` and `composer pp:*` as alias for all commands.

| Command                 | Alias        | Description                                                            |
|-------------------------|--------------|------------------------------------------------------------------------|
| `plug-and-play`         | `pp`         | Installs plug and play dependencies together with project dependencies |
| `plug-and-play:add`     | `pp:add`     | Require a package into `packages/composer.json`                        |
| `plug-and-play:dump`    | `pp:dump`    | Same as `composer dump-autoload`, but using `plug-and-play` files      |
| `plug-and-play:init`    | `pp:init`    | Initialize plug and play plugin                                        |
| `plug-and-play:install` | `pp:install` | Same as `composer install`, but using `plug-and-play` files            |
| `plug-and-play:reset`   | `pp:reset`   | Remove `plug-and-play` files                                           |
| `plug-and-play:run`     | `pp:run`     | Same as `composer run-script`, but using `plug-and-play` files         |
| `plug-and-play:update`  | `pp:update`  | Same as `composer update`, but using `plug-and-play` files             |
| `plug-and-play:unplug`  | `pp:unplug`  | Dumps the autoloader as if the plugged packages were not installed     |

`plug-and-play:install`, `plug-and-play:update`, `plug-and-play:dump` and `plug-and-play:run` accept the same arguments
and options as the Composer commands they mirror.

### Examples

```bash
# Install, or update when packages/plug-and-play.lock is missing or out of date
composer pp

# Update only one package, keeping everything else locked
composer pp:update your-org/your-package

# Install without dev dependencies, as in production
composer pp:install --no-dev

# Optimized autoloader with the plugged packages
composer pp:dump --optimize

# Run a script, with the plugged packages in the autoloader
composer pp:run test

# Require a package from Packagist only while plugged (version defaults to "*")
composer pp:add laravel/telescope ^5.0
composer pp

# Remove every generated file, back to the committed project
composer pp:reset
```

## Configuration

`packages/composer.json` is a regular `composer.json`: anything you put there is merged into the project while plugged
(see [How It Works](#how-it-works)). The file created by `plug-and-play:init` is:

```json
{
    "config": {
        "allow-plugins": {
            "dex/composer-plug-and-play": true
        }
    },
    "extra": {
        "composer-plug-and-play": {
            "ignore": []
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

The plugin options go under the `extra.composer-plug-and-play` key, in `packages/composer.json` or in your project's
`composer.json`.

### Ignore plugged packages

Sometimes you may need to ignore a package that is under development, keeping it in `packages/` but using the version
your project requires:

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

The output lists it apart:

```
Plugged packages
  Plugged: vendor-name/other-package
Ignored packages
  Ignored: vendor-name/package-to-ignore
```

### Require dev dependencies from plugged packages

Composer only installs the `require-dev` of the root package. When developing a package or library you may need its
dev dependencies, for instance to run its own test suite:

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

Every `require-dev` entry of `packages/vendor-name/package-to-require-dev/composer.json` is required by the project
while plugged.

### Autoload dev dependencies from plugged packages

In the same way, the `autoload-dev` of a plugged package is not autoloaded unless you ask for it:

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

Given a package with:

```json
{
    "name": "vendor-name/package-to-autoload-dev",
    "autoload-dev": {
        "psr-4": {
            "VendorName\\Package\\Tests\\": "tests/"
        }
    }
}
```

the project autoloads `VendorName\Package\Tests\` from `packages/vendor-name/package-to-autoload-dev/tests/`. Only
`psr-4` entries are supported.

## Recipes

### Develop a package the project already requires

Your project requires `acme/billing` from Packagist and you need to fix a bug in it:

```bash
git clone git@github.com:acme/billing.git packages/acme/billing
composer pp
```

The local clone replaces the Packagist version in the project. When the fix is released, remove the clone and run
`composer pp:reset` and `composer install`.

### Try a branch or a fork without cloning it

Point `packages/composer.json` to the repository and require the branch:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-user/billing"
        }
    ],
    "require": {
        "acme/billing": "dev-fix-invoices"
    }
}
```

```bash
composer pp
```

### Work on a package and run its tests from the project

```json
{
    "extra": {
        "composer-plug-and-play": {
            "require-dev": ["acme/billing"],
            "autoload-dev": ["acme/billing"]
        }
    }
}
```

```bash
composer pp
vendor/bin/phpunit packages/acme/billing/tests
```

### Run the project's test suite as its CI does

```bash
composer pp:unplug
vendor/bin/phpunit
composer pp:dump
```

See [Unplugging Packages](#unplugging-packages).

## Unplugging Packages

Sometimes the project must run as if nothing were plugged, for instance to run its test suite the way its CI does,
where `packages/` is empty. `plug-and-play:unplug` does that without uninstalling anything:

```bash
composer pp:unplug                       # as if no package were plugged
composer pp:unplug --keep=acme/blog      # only acme/blog (and what it requires) stays plugged
composer pp:unplug --keep=acme/blog --keep=acme/shop
composer pp:dump                         # back to everything plugged
```

The output tells what stays and what was hidden:

```
Plugged packages
  Plugged: acme/blog
Unplugged packages
  Unplugged: acme/shop
```

It removes from `vendor/composer/installed.json` (and `installed.php`) the packages that are there only because of
plug and play, then dumps the autoloader and runs the project's `post-autoload-dump` scripts. Everything that reads
the installed repository stops seeing them: the autoloader (PSR-4, classmap and files), `Composer\InstalledVersions`
and, in Laravel, `package:discover`, so their service providers, routes and migrations are not registered.

- A package is hidden only if nothing that stays needs it: the project's own packages and the `--keep` ones keep
  their dependencies.
- Only the plugged packages are listed; the dependencies they brought along are hidden with them.
- Nothing is deleted from `vendor` or `packages`. A copy of the installed repository is kept in
  `packages/plug-and-play.installed.json` and `.php`, and **every plug and play command starts by bringing it back**,
  so `pp:dump`, `pp:install` or `plug-and-play` plug everything again. `pp:unplug` itself always starts from the full
  state, so it can be run again with other `--keep` options.
- While unplugged, the whole checkout behaves that way, including a development server running from it.
- When plug and play installed a different version of a dependency the project also uses, that version stays in
  `vendor`: unplug only hides, it does not reinstall. For a byte-for-byte CI environment, use a clean
  `composer install`.

## The Manifest File

Every install, update and autoload dump writes `packages/plug-and-play.php`, a plain PHP file your application can
`require` to find out which packages are only there because of plug and play.

```php
<?php return [
    'plugged' => ['acme/blog'],
    'ignored' => ['acme/draft'],
    'installed' => ['acme/blog', 'league/commonmark'],
    'unplugged' => [],
];
```

- `plugged` — the packages found under `packages/<vendor>/<package>` and the ones required by
  `packages/composer.json`.
- `ignored` — the plugged packages listed in `extra.composer-plug-and-play.ignore`.
- `installed` — `plugged` plus every dependency they dragged in, computed as the difference between
  `packages/plug-and-play.lock` and your real `composer.lock`. **This is the list you want**: it is what would
  disappear from `vendor` if you ran `plug-and-play:reset`.
- `unplugged` — the packages that `plug-and-play:unplug` is hiding right now, dependencies included.

`plug-and-play:reset` deletes the file, so its absence simply means nothing is plugged:

```php
$file = __DIR__ . '/packages/plug-and-play.php';

$manifest = is_file($file) ? require $file : ['installed' => [], 'unplugged' => []];

// Packages present right now only because of plug and play
$plugged = array_diff($manifest['installed'], $manifest['unplugged']);

if (in_array('laravel/telescope', $plugged, true)) {
    // ...
}
```

## Contributing

Contributions are welcome. Please open an issue before submitting a pull request so the change can be discussed first.

```bash
composer test       # run all tests
composer coverage   # run all tests with coverage (requires Xdebug)
composer format     # format code with Laravel Pint (PSR-12)
```

## License

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) is licensed under the MIT license.
See the [license](https://github.com/edersoares/composer-plug-and-play/blob/main/LICENSE.md) file for more details.
