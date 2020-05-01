<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WxPaymentLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wx-payment-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'raw')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'order')->textInput() ?>

    <?= $form->field($model, 'user')->textInput() ?>

    <?= $form->field($model, 'headers')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'get')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'post')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'method')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
