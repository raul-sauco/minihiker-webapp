<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */

echo Html::beginTag('div', [
    'class' => 'form-group field-programgroup-weapp_display_image'
]);
echo Html::label(
    $model->getAttributeLabel('weapp_cover_image'),
    'programgroup-weapp_cover_image',
    ['class' => 'control-label']
);
echo Html::activeHiddenInput($model, 'weapp_cover_image', []);

if (!empty($model->weapp_cover_image)) {
    $imgSrc = '@imgUrl/pg/' . $model->id . '/' . $model->weapp_cover_image;
    $btnText = Yii::t('app', 'Update Cover Image');
} else {
    $imgSrc = '@imgUrl/no-image.jpg';
    $btnText = Yii::t('app', 'Select Cover Image');
}

echo Html::img($imgSrc, [
    'id' => 'pg-weapp-cover-image',
    'alt' => Yii::t('app',
        '{program-group}\'s cover image',
        ['program-group' => $model->weapp_display_name]),
    'data-url' => Url::to('@imgUrl/pg/' . $model->id . '/')
]);

echo Html::button(
    $btnText,
    [
        'class' => 'btn btn-success',
        'id' => 'update-weapp-cover-image',
        'data-pg-id' => $model->id,
        'data-url' => Url::to('@imgUrl/pg/' . $model->id . '/')
    ]
);

echo Html::endTag('div');
