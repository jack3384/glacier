<?php
return [
    'debug'=>1,//0为不显示debug信息
    'dsn'=>[
        //sqlite数据库相关配置
        'driver' => 'sqlite',
        'database' => dirname(__DIR__).'/Database/glacier.db',
        'prefix' => '',
    ]
    /*mysql数据库的demo
     'dsn'=>[
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'python',
        'username'  => 'root',
        'password'  => '123456',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => ''
    ]*/
];
