<?php

use yii\helpers\Html;

// Fetch the application user
$user = Yii::$app->user->identity;

$this->registerJsVar(
    'Mh', [
        'debug' => YII_DEBUG,
        'globalData' => [
            'debounceWait' => 500,
            'spinner20' => Html::tag('div',
                Html::img('@imgUrl/spinner-20.gif',
                    ['class' => 'loading-spinner']),
                ['class' => 'spinner-container spinner-container-20']
            ),
            'spinner50' => Html::tag('div',
                Html::img('@staticUrl/img/spinner_50.gif',
                    ['class' => 'loading-spinner']),
                ['class' => 'spinner-container spinner-container-50']
            ),
            'spinner80' => Html::tag('div',
            '<div></div><div></div><div></div><div></div>',
                ['class' => 'lds-ring-80']),
            'spinner200' => Html::tag('div',
                Html::img('@staticUrl/img/spinner_200.gif',['class' => 'loading-spinner']),
                ['class' => 'spinner-container spinner-container-200']
            ),
            'username' => $user->username ?? '',
            'accesstoken' => $user->access_token ?? '',
            'applanguage' => Yii::$app->language,
            'baseurl' => Yii::getAlias('@web/'),
            'apiurl' => Yii::$app->params['apiUrl'],
            'staticurl' => Yii::getAlias('@staticUrl/'),
            'imgurl' => Yii::getAlias('@imgUrl/'),
            'requestHeaders' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $user->access_token ?? 'guest'
            ]
        ],
        'methods' => []
    ]
);
