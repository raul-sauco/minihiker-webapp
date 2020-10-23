<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */

// Fetch the application user
$user = Yii::$app->user->identity;

$this->registerJsVar(
    'Mh', [
        'debug' => YII_DEBUG,
        'globalData' => [
            'debounceWait' => 500,
            'spinner20' => $this->render('spinner', ['size' => 20]),
            'spinner50' => $this->render('spinner', ['size' => 50]),
            'spinner80' => $this->render('spinner', ['size' => 80]),
            'spinner200' => $this->render('spinner', ['size' => 200]),
            'username' => $user->username ?? '',
            'accesstoken' => $user->access_token ?? '',
            'applanguage' => Yii::$app->language,
            'baseurl' => Yii::getAlias('@web/'),
            'apiurl' => Yii::getAlias('@apiUrl/'),
            'staticurl' => Yii::getAlias('@staticUrl/'),
            'imgurl' => Yii::getAlias('@imgUrl/'),
            'requestHeaders' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . ($user === null ? 'guest' : ($user->access_token ?? 'guest'))
            ]
        ],
        'methods' => []
    ]
);
