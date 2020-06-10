<?php

use yii\bootstrap\Html;
use yii\helpers\Markdown;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

if (!empty($model->remarks)) {
    echo Html::tag('div' ,
        Markdown::process(Html::encode($model->remarks)),
        [
            'class' => 'alert alert-info program-remarks-container' ,
            'role' => 'alert',
            'id' => "program-$model->id-remarks"
        ]);
}
