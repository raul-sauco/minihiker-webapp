<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@staticUrl' => (empty($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] === 'localhost') ?
            'http://static.mh' : 'https://static.minihiker.com',
        '@imgUrl' => '@staticUrl/img',
        '@cssUrl' => '@staticUrl/css',
        '@jsUrl' => '@staticUrl/js',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/i18n/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'man*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/i18n/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'man' => 'man.php',
                        'man/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
];
