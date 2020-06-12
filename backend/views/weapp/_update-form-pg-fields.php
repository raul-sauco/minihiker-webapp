<?php

use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */
/* @var $form ActiveForm */
?>
<div class="row">
    <div class="col-lg-6">
        <?= $form->field($model, 'weapp_visible')->dropDownList([
            0 => Yii::t('app', 'No'),
            1 => Yii::t('app', 'Yes')
        ]) ?>
    </div>
    <div class="col-lg-6">
        <?= $form->field($model, 'weapp_in_banner')->dropDownList([
            0 => Yii::t('app', 'No'),
            1 => Yii::t('app', 'Yes')
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <?= $form->field($model, 'weapp_display_name')->textInput() ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-3">
        <?= $form->field($model, 'min_age')->textInput() ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'max_age')->textInput() ?>
    </div>
    <div class="col-lg-6">
        <?= $form->field($model, 'accompanied')
            ->dropDownList([
                '0' => Yii::t('app', 'Only children'),
                '1' => Yii::t('app', 'With parents')
            ])->label(Yii::t('app', 'Program Type')) ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <?= $form->field($model, 'theme')->textInput() ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <?= $form->field($model, 'summary')->textarea(['cols' => 3]) ?>
        <?= $form->field($model, 'keywords')->textarea(['cols' => 3]) ?>
    </div>
</div>
