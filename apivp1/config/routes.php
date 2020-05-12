<?php

use yii\rest\UrlRule;

/** Url rules for apivp1 application */
return [
    ['class' => UrlRule::class, 'controller' => 'client'],
    ['class' => UrlRule::class, 'controller' => 'family'],
    ['class' => UrlRule::class, 'controller' => 'program-group'],
    ['class' => UrlRule::class, 'controller' => 'program-type'],
    ['class' => UrlRule::class, 'controller' => ['wxbp' => 'banner-program']],
    ['class' => UrlRule::class, 'controller' => ['wxcpi' => 'client-passport-image']],
    ['class' => UrlRule::class, 'controller' => ['wxps' => 'program-search']],
    ['class' => UrlRule::class, 'controller' => 'wx-payment', 'pluralize' => false],
    ['class' => UrlRule::class, 'controller' => 'wx-payment-notify', 'pluralize' => false],
];
