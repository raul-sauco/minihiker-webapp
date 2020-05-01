<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $client common\models\Client */

$link = Html::a(
    Yii::t('app', 'View'), 
    ['client/view' , 'id' => $client->id] , 
    ['class' => 'btn btn-sm btn-success']);

$message = Html::icon('exclamation-sign') . ' ' .
    Yii::t('app', 
        "The name selected already exists on the database {link}", 
        ['link' => $link]);

echo Html::tag('div' , $message , 
    [
        'class' => 'alert alert-warning',
        'id' => 'duplicate-name-warning',
    ]);