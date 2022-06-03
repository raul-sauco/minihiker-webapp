<?php

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    require __DIR__ . '/test-local.php',
    [
        'components' => [
            'request' => [
                // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
                'cookieValidationKey' => '',
            ],
            'db' => [
                // From the console access localhost
                'dsn' => 'mysql:host=127.0.0.1:34106;dbname=mh_test',
                'username' => 'mh_test_user',
                'password' => 'password',
            ],
        ],
    ]
);
