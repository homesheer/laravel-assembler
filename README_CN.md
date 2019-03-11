# Laravel Assembler

[![For Laravel 5](https://img.shields.io/badge/laravel-5.*-green.svg)](https://github.com/laravel/laravel)
[![For Lumen 5](https://img.shields.io/badge/lumen-5.*-green.svg)](https://github.com/laravel/lumen)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/homesheer/laravel-assembler.svg)](https://packagist.org/packages/homesheer/laravel-assembler)
[![Total Downloads](https://img.shields.io/packagist/dt/homesheer/laravel-assembler.svg)](https://packagist.org/packages/homesheer/laravel-assembler)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

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

1. 实例化`Assembler`或继承自`Assembler`的子类并传入`Eloquent`模型或`DTO`:

```php
$assembler = new Assembler(User::find($id));
```

2. 调用`Assembler`的`getAssembledData`方法获取组装后的数据：

```php
$assembledUserData = $assembler->getAssembledData();
```

3. 在url添加查询字符串`fields`并指定要获取的字段：

在要请求的`controller`方法返回上一步的`$assembledUserData`

```php
return $this->response($assembledUserData);
```

请求API接口，在`fields`查询字符串中指定字段

```
// 获取ID为1的用户的`name`和`gender`字段信息
http://localhost/users/1?fields={name,gender}
```

4. 还可以获取`Eloquent Model`或`DTO`中不存在的属性：

只要新建一个类继承自`Assembler`并定义获取虚拟属性的方法

```php
class UserAssembler extends Assembler
{
    public function getVirtualField()
    {
        return 'VirtualField';
    }
}
```

然后在请求中加入这个虚拟字段

```
http://localhost/users/1?fields={name,gender,virtualField}
```

5. 支持3种类型的源数据，有`getter`方法的对象，无`getter`方法但有`public`属性的对象，还有关联数组：

有`getter`方法的对象：

```php
class User
{
    public function getName()
    {
        return 'Mars';
    }
}
```

无`getter`方法但有`public`属性的对象:

```php
class User
{
    public $name = 'Mars';
}
```

关联数组:

```php
$user = [
    'name' => 'Mars',
];
```

6. 优先级：

获取字段会以以下的顺序从高到低获取，直到有这个字段终止，获取不到则会默认返回`null`
- 自定义`Assembler`中的`getter`方法
- `Eloquent Model`或`DTO`中`getter`方法
- `Eloquent Model`或`DTO`中的属性

7. 嵌套级联：

如果是`Eloquent Model`, 天然支持获取关联的模型属性，还可以获取自定义`getter`方法或属性返回对象或数组

```
// address是user模型的关联对象
http://localhost/users/1?fields={name,gender,address{province,city}}
```

如果是`DTO`，则需要自己定义`getter`方法或属性返回的是对象或数组

如果是关联数组，只要是多维关联数组即可

为了响应速度，不建议嵌套超过5层

    
## 贡献

欢迎参与贡献, [致谢这些贡献者](https://github.com/homesheer/laravel-assembler/graphs/contributors) :)

## 许可证

Laravel Assembler 使用 [MIT 许可证](http://opensource.org/licenses/MIT) 开源.