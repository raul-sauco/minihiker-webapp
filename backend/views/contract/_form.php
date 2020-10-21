<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Contract */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contract-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?/*= $form->field($model, 'contractno')->textInput(['maxlength' => true]) */?>

    <?= $form->field($model, 'contractfile')->fileInput() ?>

    <?= $form->field($model, 'contracttitle')->textInput(['maxlength' => true]) ?>

    <?/*= $form->field($model, 'touid')->textInput() */?>

    <?/*= $form->field($model, 'status')->textInput() */?>

    <?/*= $form->field($model, 'cratetime')->textInput() */?>

    <?/*= $form->field($model, 'updatetime')->textInput() */?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
