<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['options' => [
            'id' => 'user-form'
    ]]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_zh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_pinyin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'birthdate')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
        ],
        'options' => ['class' => 'form-control'],
    ]) ?>

    <?= $form->field($model, 'is_male')->checkbox() ?>

    <?= $form->field($model, 'id_card_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'passport_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'passport_issue_date')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
        ],
        'options' => ['class' => 'form-control'],
    ]) ?>

    <?= $form->field($model, 'passport_expire_date')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
        ],
        'options' => ['class' => 'form-control'],
    ]) ?>

    <?= $form->field($model, 'passport_place_of_issue')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'passport_issuing_authority')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'place_of_birth')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_type')->dropDownList(
		    		\common\helpers\UserHelper::getUserTypeLabels(),
		    		['prompt' => \Yii::t('app', 'Select user type'),]
		   	) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
