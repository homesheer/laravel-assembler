Laravel Assembler

[![Latest Version on Packagist](https://img.shields.io/packagist/v/homesheer/laravel-assembler.svg?style=flat-square)](https://packagist.org/packages/homesheer/laravel-assembler)
[![Total Downloads](https://img.shields.io/packagist/dt/homesheer/laravel-assembler.svg?style=flat-square)](https://packagist.org/packages/homesheer/laravel-assembler)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## 介绍

`Laravel Assembler`用于前端请求时获取`Eloquent Model`或`DTO`指定的字段，减少API接口升级的需求

## 要求
`Laravel 5.4`或更新的版本

## 安装

使用`Composer`:

``` bash
composer require homesheer/laravel-assembler
```

发布配置文件:

```bash
php artisan vendor:publish --provider="HomeSheer\LaravelAssembler\AssemblerServiceProvider" --tag="config"
```

`Laravel 5.4`或更低的版本:

```php
// config/app.php
HomeSheer\LaravelAssembler\AssemblerServiceProvider::class,
```

Lumen:

```php
// bootstrap/app.php
$app->register(HomeSheer\LaravelAssembler\AssemblerServiceProvider::class);
```

## 用法

1. 实例化`Assembler`并传入`Eloquent`模型或`DTO`:

```php
$assembler = new Assembler(User::find($id));
```

2. 调用`Assembler`的`getAssembledData`方法获取组装后的数据：

```php
$assembledUserData = $assembler->getAssembledData();
```

3. 在url添加查询字符串`fields`并指定要获取的字段：

```
// 获取ID为1的用户的`name`和`gender`字段信息
http://localhost/users/1?fields={name,gender}
```
    
## 贡献

欢迎参与贡献, [致谢这些贡献者](https://github.com/homesheer/laravel-assembler/graphs/contributors) :)

## 许可证

Laravel Assembler 使用 [MIT 许可证](http://opensource.org/licenses/MIT) 开源.