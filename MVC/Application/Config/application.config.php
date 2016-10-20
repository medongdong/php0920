<?php
return [
    'db'=>[
        'host'=>'127.0.0.1',
        'username'=>'root',
        'password'=>'root',
        'dbname'=>'shop',
        'port'=>3306,
        'charset'=>'utf8',
        'prefix'=>''//表前缀 eg: it_
    ],
    'default'=>[
        'platform'=>'Admin',
        'controller'=>'Index',
        'action'=>'index'
    ],
    'upload'=>[
        'max_size'=>1024*1024*2,//2M
        'allow_types'=>["image/jpeg","image/png","image/gif","image/bmp"]
    ]
];
