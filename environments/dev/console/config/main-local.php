<?php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=127.0.0.1:34107;dbname=mhdb',
            'username' => 'mh_user',
            'password' => 'password',
        ],
    ],
];
