<?php

use common\log\DbTarget;
use yii\log\FileTarget;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'locale' => 'zh-CN',
            'currencyCode' => 'CNY'
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
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                ],
                [
                    'class' => DbTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                ],
//                [
//                    'class' => EmailTarget::class,
//                    'levels' => ['error'],
//                    'categories' => ['yii\db\*'],
//                    'message' => [
//                        'from' => ['log@example.com'],
//                        'to' => ['admin@example.com', 'developer@example.com'],
//                        'subject' => 'Database errors at example.com',
//                    ],
//                ],
            ],
        ],
    ],
];
