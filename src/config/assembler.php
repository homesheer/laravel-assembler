<?php

return [
    /*
    |--------------------------------------------------------------------------
    | url查询字符串名
    |--------------------------------------------------------------------------
    |
    | 通过下面配置的查询字符串名指定要获取的字段，如指定字符串名为fields，
    | 要获取user上的id，name和值对象address的province, city字段，url格式如下：
    | http://localhots/user?fields={id,name,address{province,city}}
    */

    'query_name' => 'fields',

    /*
    |--------------------------------------------------------------------------
    | assembler类中获取字段数据的方法名前缀
    |--------------------------------------------------------------------------
    |
    | 通过下面配置的查询字符串名指定要获取的字段，如指定字符串名为fields，
    | 要获取user上的id，name和值对象address的province, city字段，url格式如下：
    | http://localhots/user?fields={id,name,address{province,city}}
    */

    'getter_name_prefix' => 'get',

    /*
    |--------------------------------------------------------------------------
    | 配置eloquent model或DTO与Assembler的对应关系
    |--------------------------------------------------------------------------
    |
    | 数组的key为eloquent model或DTO，value为对应的Assembler， 类名都必须为全名，格式如：
    | 'maps' => [
    |     'App\User' => 'App\Assembler\UserAssembler',
    | ];
    */

    'maps' => [

    ],

];
