<?php

use common\models\ProgramGroup;
use yii\bootstrap\Html;
use yii\web\View;

/* @var $this View */
/* @var $model ProgramGroup */

foreach ($model->images as $image) {
    echo Html::img(
        "@imgUrl/pg/$model->id/th/" . $image->name, [
        'alt' => $image->name,
        'class' => 'img-thumbnail'
    ]);
}
