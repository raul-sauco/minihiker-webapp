<?php
return [
    'id' => 'app-common-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'formatter' => [
            'locale' => 'en-US',
            'currencyCode' => 'CNY'
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
        ],
    ],
];
