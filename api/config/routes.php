<?php

use yii\rest\UrlRule;

/** Url rules for apivp1 application */
return [
    ['class' => UrlRule::class, 'controller' => 'client'],
    ['class' => UrlRule::class, 'controller' => 'client-search', 'pluralize' => false],
    ['class' => UrlRule::class, 'controller' => 'family'],
    ['class' => UrlRule::class, 'controller' => 'payment'],
    ['class' => UrlRule::class, 'controller' => 'program-client'],
    ['class' => UrlRule::class, 'controller' => 'program-family'],
    ['class' => UrlRule::class, 'controller' => 'program-search', 'pluralize' => false],
];
