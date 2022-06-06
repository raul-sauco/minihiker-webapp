<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\search\LogSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="log-index-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'id' => 'log-index-search-form',
        ],
    ]); ?>
    <?= $form->field($model, 'prefix') ?>
    <?= $form->field($model, 'level') ?>
    <?= $form->field($model, 'category') ?>
    <?= $form->field($model, 'log_time') ?>
    <?= $form->field($model, 'message') ?>
    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Search'),
            ['class' => 'btn btn-primary'],
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
