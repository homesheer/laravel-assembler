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

### 1. Instantiate `Assembler` or a subclass inherited from `Assembler` and pass in `Eloquent` model or `DTO`:

```php
$assembler = new Assembler(User::find($id));
```

### 2. Call the `getAssembledData` method of `Assembler` to obtain the assembled data:

```php
$assembledUserData = $assembler->getAssembledData();
```

### 3. Add the query string `fields` to the URL and specify the fields to be acquired:

The `controller` method of request returns the `$assembledUserData` of the previous step.

```php
return $this->response($assembledUserData);
```

Request API interface to specify fields in the `fields` query string

```
// Get name and gender field information for users with ID 1
Http://localhost/users/1?fields={name,gender}
```
   
### 4. Attributes that do not exist in `Eloquent Model` or `DTO` can also be obtained:   

Simply create a new class that inherits from `Assembler` and defines the method of getting the virtual attributes

```php
class UserAssembler extends Assembler
{
    public function getVirtualField()
    {
        return 'VirtualField';
    }
}
```

Then add the virtual field to the request

```
http://localhost/users/1?fields={name,gender,virtualField}
```

### 5. Supports three types of source data: objects with `getter` methods, objects without `getter` methods but with `public` attributes, and associative arrays:

Objects with `getter` methods:

```php
class User
{
    public function getName()
    {
        return 'Mars';
    }
}
```

Objects without `getter` methods but with `public` attributes:

```php
class User
{
    public $name = 'Mars';
}
```

Association array:

```php
$user = [
    'name' => 'Mars',
];
```

### 6. Priority:

The acquired field is retrieved from high to low in the following order until the field terminates and returns `null` by default if it is not retrieved.
- Customize the `getter` method in `Assembler`
- The `getter` method in `Eloquent Model` or `DTO`
- Attributes in `Eloquent Model` or `DTO`

### 7. Nested Cascade:

In the case of `Eloquent Model`, natural support is provided for acquiring associated model attributes, as well as customized `getter` methods or attributes to return objects or arrays.

```
// Address is the associated object of user model
http://localhost/users/1?fields={name,gender,address{province,city}}
```

In the case of `DTO`, you need to define your own `getter` method or attribute that returns objects or arrays.

If it's an associative array, it's just a multidimensional associative array.

For response speed, it is not recommended to nest more than five layers
    
## Contributing

Contributions are welcome, [thanks to y'all](https://github.com/homesheer/laravel-assembler/graphs/contributors) :)

## License

Laravel Assembler is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).