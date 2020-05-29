<?php

use yii\rest\UrlRule;

/** Url rules for apivp1 application */
return [
    ['class' => 'yii\rest\UrlRule', 'controller' => ['bu' => 'blue-imp-program-group-image-upload']],
    ['class' => UrlRule::class, 'controller' => 'client'],
    ['class' => UrlRule::class, 'controller' => 'client-search', 'pluralize' => false],
    ['class' => UrlRule::class, 'controller' => 'family'],
    ['class' => UrlRule::class, 'controller' => 'image'],
    ['class' => UrlRule::class, 'controller' => 'payment'],
    ['class' => UrlRule::class, 'controller' => 'program-client',
        'extraPatterns' => [
            'GET,HEAD {program-id}/{client-id}' => 'view',
            'OPTIONS {program-id}/{client-id}' => 'options'
        ],
        'tokens' => [
            '{program-id}' => '<program_id:\\d[\\d,]*>',
            '{client-id}' => '<client_id:\\d[\\d,]*>'
        ]
    ],
    ['class' => UrlRule::class, 'controller' => 'program-family',
        'extraPatterns' => [
            'GET,HEAD {program-id}/{family-id}' => 'view',
            'OPTIONS {program-id}/{family-id}' => 'options'
        ],
        'tokens' => [
            '{program-id}' => '<program_id:\\d[\\d,]*>',
            '{family-id}' => '<family_id:\\d[\\d,]*>'
        ]
    ],
    ['class' => UrlRule::class, 'controller' => 'program-group-image-download'],
    ['class' => UrlRule::class, 'controller' => 'program-search', 'pluralize' => false],
];
