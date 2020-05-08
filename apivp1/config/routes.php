<?php
/** Url rules for apivp1 application */
return [
    ['class' => 'yii\rest\UrlRule', 'controller' => 'program-group'],
    ['class' => 'yii\rest\UrlRule', 'controller' => 'program-type'],
    ['class' => 'yii\rest\UrlRule', 'controller' => ['wxbp' => 'banner-program']],
    ['class' => 'yii\rest\UrlRule', 'controller' => ['wxps' => 'program-search']],
];
