<?php

/* @var $this \yii\web\View */

$this->registerJsVar('Mh', [
    'debug' => YII_DEBUG,
    'globalData' => [
        'yht' => [
            'url' => Yii::$app->params['yunhetongUrl'],
            'appId' => Yii::$app->params['yunhetongAppId'],
            'appKey' => Yii::$app->params['yunhetongAppKey'],
        ]
    ],
]);
