<?php

use yii\rest\UrlRule;

/** Url rules for apivp1 application */
return [
    ['class' => UrlRule::class, 'controller' => 'account-info', 'pluralize' => false],
    ['class' => UrlRule::class, 'controller' => 'client'],
    ['class' => UrlRule::class, 'controller' => 'family'],
    [
        'class' => UrlRule::class,
        'controller' => 'participant',
        'extraPatterns' => [
            'GET {program_id}' => 'view',
            'POST {client_id}/{program_id}' => 'create',
            'DELETE {client_id}/{program_id}' => 'delete'
        ],
        'tokens' => [
            '{client_id}' => '<client_id:\\d[\\d,]*>',
            '{program_id}' => '<program_id:\\d[\\d,]*>'
        ]
    ],
    ['class' => UrlRule::class, 'controller' => 'program'],
    ['class' => UrlRule::class, 'controller' => 'program-group'],
    ['class' => UrlRule::class, 'controller' => 'program-type'],
    ['class' => UrlRule::class, 'controller' => 'qa'],
    ['class' => UrlRule::class, 'controller' => 'weapp-log'],
    ['class' => UrlRule::class, 'controller' => 'wx-auth', 'pluralize' => false],
    ['class' => UrlRule::class, 'controller' => ['wxua' => 'family-avatar']],
    ['class' => UrlRule::class, 'controller' => ['wxbp' => 'banner-program']],
    ['class' => UrlRule::class, 'controller' => ['wxcpi' => 'client-passport-image']],
    ['class' => UrlRule::class, 'controller' => ['wxps' => 'program-search']],
    ['class' => UrlRule::class, 'controller' => ['wxrp' => 'wx-program-recommendations']],
    ['class' => UrlRule::class, 'controller' => ['wxvh' => 'wx-program-visit-history']],
    ['class' => UrlRule::class, 'controller' => 'wx-payment', 'pluralize' => false],
    ['class' => UrlRule::class, 'controller' => 'wx-payment-notify', 'pluralize' => false],
    ['class' => UrlRule::class, 'controller' => 'wx-unified-payment-order'],
	['class' => UrlRule::class, 'controller' => 'authentic'],
	['class' => UrlRule::class, 'controller' => 'sms'],
	['class' => UrlRule::class, 'controller' => 'contract','extraPatterns'=>[
		'POST notify' => 'notify',
		'GET scount' => 'scount',
		'GET list' => 'list',
	]],
];
