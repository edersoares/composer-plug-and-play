# Composer Plug and Play

<a href="https://github.com/edersoares/composer-plug-and-play/actions"><img src="https://img.shields.io/github/actions/workflow/status/edersoares/composer-plug-and-play/tests.yml?branch=main&label=tests" alt="Tests" /></a>
<a href="https://github.com/edersoares/composer-plug-and-play/releases"><img src="https://img.shields.io/github/release/edersoares/composer-plug-and-play.svg?label=latest%20release" alt="Latest Release" /></a>
<a href="https://github.com/edersoares/composer-plug-and-play/blob/master/LICENSE"><img src="https://img.shields.io/github/license/edersoares/composer-plug-and-play" alt="License" /></a>

Adds to [Composer](https://getcomposer.org/), PHP's dependency manager, the ability to plug and play packages without necessarily installing 
a new dependency on `composer.json`.

## Installation

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) requires [Composer](https://getcomposer.org/)
`2.3.0` or newer.

```bash
composer require dex/composer-plug-and-play
```

### Global installation

You can install [Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) globally to use its abilities in all your local projects.

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

You can use `composer pp` and `composer pp:*` as alias for all commands.

- `plug-and-play`: installs plug and play dependencies together project dependencies.
- `plug-and-play:add`: require a package into `packages/composer.json`.
- `plug-and-play:dump`: same that `composer dump-autoload`, but using `plug-and-play` files.
- `plug-and-play:init`: initialize plug and play plugin.
- `plug-and-play:install`: same that `composer install`, but using `plug-and-play` files.
- `plug-and-play:reset`: remove `plug-and-play` files.
- `plug-and-play:run`: same that `composer run-script`, but using `plug-and-play` files.
- `plug-and-play:update`: same that `composer update`, but using `plug-and-play` files.

### Directories and files

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) plugin needs a `packages` folder in 
the project root directory where the plug and play structure will live.

The `plug-and-play.json` and `plug-and-play.lock` files will contain the real project dependencies and plug and play 
dependencies.

Your project directory will look like this:

```
|- packages 
|  |- <vendor-name>
|  |  |- <plug-and-play-package>
|  |     |- composer.json
|  |     |- composer.lock
|  |- composer.json
|  |- plug-and-play.json
|  |- plug-and-play.lock
|
|- vendor
|  |- <vendor-name>
|     |- <require-package>
|        |- composer.json
|        |- composer.lock
|
|- composer.json
|- composer.lock
```

### Ignore plugged packages

Sometimes you may need to ignore a package that is under development, for that adds in `packages/composer.json` 
something like this and run `composer plug-and-play`.

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

### Require dev dependencies from plugged packages

When developing some package or library you may need to require its dev dependencies, for that adds in 
`packages/composer.json` something like this and run `composer plug-and-play` that the `require-dev` dependencies will 
be installed.

```json 
{
    "extra": {
        "composer-plug-and-play": {
            "require-dev": [
                "dex/fake"
            ]
        }
    }
}
```

### Autoload (strategy)

You may have some problems with symlinks and recursion when developing packages inside another application or package, 
for that, you can use `experimental:autoload` strategy.

This strategy will create a simple copy of your `composer.json` in `packages/vendor` directory to do a symlink from your 
original `vendor` directory.

To activate it, you should change your `packages/composer.json`.

```json 
{
    "extra": {
        "composer-plug-and-play": {
            "autoload-dev": ["dex/fake"],
            "require-dev": ["dex/fake"],
            "strategy": "experimental:autoload"
        }
    }
}
```

You must add to `autoload-dev` the packages that you want to map its autoload and add to `require-dev` the packages
that you want to require its dev dependencies.

## License

[Composer Plug and Play](https://github.com/edersoares/composer-plug-and-play/) is licensed under the MIT license.
See the [license](https://github.com/edersoares/composer-plug-and-play/blob/main/LICENSE.md) file for more details.
