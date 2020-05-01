<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Supplier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="supplier-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-md-6">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'remarks')->textarea(['rows' => 3]) ?>

        </div>

        <div class="col-md-6">

            <?= $form->field($model, 'address')->textarea(['rows' => 2]) ?>

            <?= $form->field($model, 'address_2')->textarea(['rows' => 2]) ?>

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
