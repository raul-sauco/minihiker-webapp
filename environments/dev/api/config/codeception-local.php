<?php

return yii\helpers\ArrayHelper::merge(
    require dirname(__DIR__, 2) . '/common/config/codeception-local.php',
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    require __DIR__ . '/test-local.php',
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=127.0.0.1:34106;dbname=mh_test',
                'username' => 'mh_test_user',
                'password' => 'password',
            ],
        ],
    ]
);
