<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramPrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div id="program-price-form-container">

    <?php $form = ActiveForm::begin([
            'options' => [
                    'id' => 'program-price-form'
            ]
    ]); ?>

    <?= Html::activeHiddenInput($model, 'id') ?>

    <?= Html::activeHiddenInput($model, 'program_id') ?>

    <?= $form->field($model, 'adults')->textInput([
        'type' => 'number',
        'min' => 0,
        'max' => 20
    ]) ?>

    <?= $form->field($model, 'kids')->textInput([
        'type' => 'number',
        'min' => 0,
        'max' => 20
    ]) ?>

    <?= $form->field($model, 'membership_type')->dropDownList([
        0 => '非会员',
        1 => '会员'
    ]) ?>

    <?= $form->field($model, 'price')->textInput([
        'type' => 'number',
        'min' => 0,
        'max' => 100000
    ]) ?>

    <div class="form-group">
        <?= Html::button(
                Yii::t('app', 'Save'),
                [
                    'class' => 'btn btn-success',
                    'onclick' => "submitProgramPriceForm($model->id)"
                ])
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
