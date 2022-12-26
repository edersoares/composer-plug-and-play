# Composer Plug and Play

<a href="https://github.com/edersoares/composer-plug-and-play/actions"><img src="https://img.shields.io/github/actions/workflow/status/edersoares/composer-plug-and-play/tests.yml?branch=main&label=tests" alt="Tests" /></a>
<a href="https://github.com/edersoares/composer-plug-and-play/releases"><img src="https://img.shields.io/github/release/edersoares/composer-plug-and-play.svg?label=latest%20release" alt="Latest Release" /></a>
<a href="https://github.com/edersoares/composer-plug-and-play/blob/master/LICENSE"><img src="https://img.shields.io/github/license/edersoares/composer-plug-and-play" alt="License" /></a>

Add to [Composer](https://getcomposer.org/), a dependency manager for PHP, the ability to plug and play packages without
necessarily installing a new dependency on `composer.json`.

## Installation

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) requires [Composer](https://getcomposer.org/)
`2.3.0` or newer.

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

Initialize a plug and play structure:

```bash
composer plug-and-play:init
```

Create or clone a [Composer](https://getcomposer.org/) package into `packages/<vendor>/<package>` folder and run:

```bash
composer plug-and-play
```

### Additional configuration

You can add some additional configuration in `packages/composer.json` to add more data in the final resolution of the 
`composer.json` file.

### Commands

All commands use the `plug-and-play.json` and `plug-and-play.lock` files as source to project dependencies instead of 
`composer.json` and `composer.lock` original files.

- `plug-and-play`: installs plug and play dependencies together project dependencies.
- `plug-and-play:init`: initialize plug and play plugin.
- `plug-and-play:add`: require a package into `packages/composer.json`.
- `plug-and-play:install`: same that `composer install`, but using `plug-and-play` files.
- `plug-and-play:update`: same that `composer update`, but using `plug-and-play` files.
- `plug-and-play:dump`: same that `composer dump-autoload`, but using `plug-and-play` files.

### Directories and files

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) plugin needs a `packages` folder in 
the project root directory where the plug and play structure will live.

The `plug-and-play.json` and `plug-and-play.lock` files will contain the real project dependencies and plug and play 
dependencies.

Your project directory will look like this:

```
|-- packages 
|   \__ <vendor-name>
|       \__ <plug-and-play-package>
|           \__ composer.json
|           \__ composer.lock
|   \__ composer.json
|   \__ plug-and-play.json
|   \__ plug-and-play.lock
|-- vendor
|   \__ <vendor-name>
|       \__ <require-package>
|           \__ composer.json
|           \__ composer.lock
|-- composer.json
|-- composer.lock
```

### Ignore plugged packages

Sometimes you may need to ignore a package that is under development, for that add in `packages/composer.json` something
like this and run `composer plug-and-play`.

```json 
{
  "extra": {
    "composer-plug-and-play": {
      "ignore": [
        "dex/fake"
      ]
    }
  }
}
```

## License

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) is licensed under the MIT license.
See the [license](https://github.com/edersoares/composer-plug-and-play/blob/master/LICENSE) file for more details.
