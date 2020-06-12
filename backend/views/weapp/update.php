<?php

use common\helpers\WxContentHelper;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */

$model_name = $model->weapp_display_name ?? $model->getNamei18n();
$this->title = Yii::t('app', 'Update Weapp Data {programName}', [
    'programName' => $model_name,
]);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Programs'),
    'url' => ['program/index']
];
$this->params['breadcrumbs'][] = [
    'label' => $model_name,
    'url' => ['program/view', 'id' => $model->getPrograms()->one()->id]
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Weapp Data'),
    'url' => ['view', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div id="program-group-weapp-data-update">
    <?= $this->render('_update-cover-image-modal') ?>
    <?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>
    <?php $form = ActiveForm::begin(['id' => 'program-group-form']); ?>
    <div class="row">
        <div class="col-lg-8 left-container">
            <?= $this->render('_update-form-pg-fields', [
                    'model' => $model,
                    'form' => $form
            ]) ?>
        </div>
        <div class="col-lg-4 right-container">
            <?= $this->render('_update-cover-display', ['model' => $model]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <?= $this->render('_update-description-tabs',
                ['form' => $form, 'model' => $model]) ?>
        </div>
        <div class="col-lg-4">
            <?= $this->render('_update-blueimp-images-widget',
                ['model' => $model]) ?>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-12">
            <?= Html::submitButton(
                Yii::t('app', 'Save'),
                ['class' => 'btn btn-success']
            ) ?>
        </div>
    </div>
    <?php
    echo Html::submitButton(
        Yii::t('app', 'Save'),
        ['class' => 'btn btn-success btn-lg fixed-position-save-button']
    );
    ActiveForm::end();
    if (WxContentHelper::hasRemoteImages($model)) {
        echo Html::button(
            Yii::t('app', 'Download images'),
            [
                'class' => 'btn btn-success btn-lg',
                'id' => 'download-images-button',
                'data-pg-id' => $model->id
            ]
        );
    }
    ?>
</div>
