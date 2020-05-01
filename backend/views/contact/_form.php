<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Contact */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'contact-form']]); ?>

    <?= Html::activeHiddenInput($model, 'supplier_id'); ?>

    <div class="row">

        <div class="col-md-4">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'role')->textInput(['maxlength' => true]) ?>

        </div>

        <div class="col-md-4">

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'wechat_id')->textInput(['maxlength' => true]) ?>

        </div>

        <div class="col-md-4">

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'remarks')->textarea(['rows' => 2]) ?>

        </div>

    </div>

    <div class="row">

        <div class="col-md-12">

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'),
                    ['class' => 'btn btn-success']) ?>
            </div>

        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
