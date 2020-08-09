<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WeappLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="weapp-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'res')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'extra')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'level')->textInput() ?>

    <?= $form->field($model, 'page')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'line')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'timestamp')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
