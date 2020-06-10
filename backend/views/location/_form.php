<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'location-form']]); ?>

    <?= $form->field($model, 'name_zh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'international')
        ->dropDownList([
            '0' => Yii::t('app', 'National'),
            '1' => Yii::t('app', 'International')
        ])->label(Yii::t('app', 'Area'))
    ?>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Save'),
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
