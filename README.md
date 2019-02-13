# Laravel Assembler

[中文说明](https://github.com/homesheer/laravel-assembler/blob/master/README_CN.md)

[![For Laravel 5](https://img.shields.io/badge/laravel-5.*-green.svg)](https://github.com/laravel/laravel)
[![For Lumen 5](https://img.shields.io/badge/lumen-5.*-green.svg)](https://github.com/laravel/lumen)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/homesheer/laravel-assembler.svg)](https://packagist.org/packages/homesheer/laravel-assembler)
[![Total Downloads](https://img.shields.io/packagist/dt/homesheer/laravel-assembler.svg)](https://packagist.org/packages/homesheer/laravel-assembler)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

## Introduction

Laravel Assembler is used to retrieve specifying fields of `Eloquent Model` or `DTO` for front-end requests, reducing the need for API interface upgrades.

## Requirements
This package requires Laravel 5.4 or newer.

## Installation

You can install the package via Composer:

``` bash
composer require homesheer/laravel-assembler
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="HomeSheer\LaravelAssembler\AssemblerServiceProvider" --tag="config"
```

For Laravel 5.4 or older:

```php
// config/app.php
HomeSheer\LaravelAssembler\AssemblerServiceProvider::class,
```

For Lumen:

```php
// bootstrap/app.php
$app->register(HomeSheer\LaravelAssembler\AssemblerServiceProvider::class);
```

## Usage

1. Instantiate `Assembler` and pass in `Eloquent` model or `DTO`:

```php
$assembler = new Assembler(User::find($id));
```

2. Call the `getAssembledData` method of `Assembler` to obtain the assembled data:

```php
$assembledUserData = $assembler->getAssembledData();
```

3. Add the query string `fields' to the URL and specify the fields to be acquired:

```
// Get name and gender field information for users with ID 1
Http://localhost/users/1?fields={name,gender}
```
    
## Contributing

Contributions are welcome, [thanks to y'all](https://github.com/homesheer/laravel-assembler/graphs/contributors) :)

## License

Laravel Assembler is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).