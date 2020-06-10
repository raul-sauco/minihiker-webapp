<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ImportError */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="import-error-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'field')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'field_value')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'validation_errors')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'family_id')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Save'),
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
