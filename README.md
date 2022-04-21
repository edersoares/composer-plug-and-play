# Composer Plug and Play

<a href="https://github.com/edersoares/composer-plug-and-play/actions"><img src="https://github.com/edersoares/composer-plug-and-play/workflows/tests/badge.svg" alt="Tests" /></a>
<a href="https://github.com/edersoares/composer-plug-and-play/blob/master/LICENSE"><img src="https://img.shields.io/github/license/edersoares/composer-plug-and-play" alt="License" /></a>

Add to [Composer](https://getcomposer.org/), a dependency manager for PHP, the ability to plug and play packages without
necessarily installing a new dependency on `composer.json`.

## Installation

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) requires [Composer](https://getcomposer.org/)
`2.0.0` or newer.

```bash
composer require dex/composer-plug-and-play
```

### Global installation

You can install [Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) globally to use its
abilities in all your local projects.

```bash
composer global require dex/composer-plug-and-play
```

## Usage

Create or clone a [Composer](https://getcomposer.org/) package into `packages/<vendor>/<package>` folder and run:

```bash
composer plug-and-play
```

### Another commands

All commands use `composer-plug-and-play.json` and `composer-plug-and-play.lock` instead of `composer.json` and 
`composer.lock` files as root package dependencies.

- `plug-and-play:install`: same that `composer install`, but using `composer-plug-and-play` files.
- `plug-and-play:update`: same that `composer update`, but using `composer-plug-and-play` files.
- `plug-and-play:dump`: same that `composer dump-autoload`, but using `composer-plug-and-play` files.

### Directories and files

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) plugin needs a `packages` directory in
the project's root directory.

It will create `composer-plug-and-play.json` and `composer-plug-and-play.lock` files in the project's root directory
that will contain the real project dependencies and plug and play dependencies.

Your root directory will look like this:

```
packages
\_ <vendor>
  \_ <plug-and-play-package>
    \_ composer.json
vendor
\_ <vendor>
  \_ <require-package>
    \_ composer.json
composer.json
composer.lock
composer-plug-and-play.json
composer-plug-and-play.lock
```

## License

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) is licensed under the MIT license.
See the [license](https://github.com/edersoares/composer-plug-and-play/blob/master/LICENSE) file for more details.
