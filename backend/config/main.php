<?php

use common\models\User;

$controllersList = 'program|client|family|location|program-price|user|weapp|weapp-log|wx-payment-log|' .
    'wx-unified-payment-order|wx-payment-log|contract|';

$rules = [
    "<controller:($controllersList)>/create" => '<controller>/create',
    "<controller:($controllersList)>/<id:\d+>/<action:(update|delete|export)>" => '<controller>/<action>',
    "<controller:($controllersList)>/<id:\d+>" => '<controller>/view',
    "<controller:($controllersList)>s" => '<controller>/index',
    'family/<id:\d+>/merge-search' => 'family/merge-search',
    'family/<id:\d+>/merge-confirm' => 'family/merge-confirm',
    'program-group/<id:\d+>/<action:(weapp-update|weapp-view|qas)>' => 'program-group/<action>',
    'contracts/token' => 'contract/token',
    'contracts/company' => 'contract/company',
    'contracts/query' => 'contract/query',
    'contracts/usersign' => 'contract/usersign'
];

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    'modules' => [],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'minihiker-app-backend',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $rules,
        ],
    ],
    'params' => $params,
];
