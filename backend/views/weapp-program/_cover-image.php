<?php

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */

use yii\bootstrap\Html;

if (!empty($model->weapp_cover_image)) {
    $src = "@imgUrl/pg/$model->id/" . $model->weapp_cover_image;
    $alt = Yii::t('app', '{item}\'s image',
        ['item' => $model->weapp_display_name]);
} else {
    $src = '@imgUrl/no-image.png';
    $alt = Yii::t('app', 'No image found');
}
echo Html::img($src, ['alt' => $alt]);
