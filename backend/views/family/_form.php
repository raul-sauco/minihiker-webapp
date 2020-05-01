<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Family */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="family-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'family-form']]); ?>

	<div class="row">
	
		<div class="col-md-4">
        
            <?= $form->field($model, 'category')->dropDownList([
                '会员' => '会员',
                '非会员' => '非会员',
            ]) ?>

            <?= $form->field($model, 'membership_date')->widget(\yii\jui\DatePicker::class, [
                'dateFormat' => 'yyyy-MM-dd',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                ],
                'options' => ['class' => 'form-control'],
            ]) ?>

            <?= $form->field($model, 'address')->textInput() ?>

            <?= $form->field($model, 'place_of_residence')->textInput(['maxlength' => true]) ?>
            
            <div id="serial-number"></div>

		</div>
	
		<div class="col-md-4">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'wechat')->textInput(['maxlength' => true]) ?>

		</div>
	
		<div class="col-md-4">

    		<?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

		</div>
	
	</div>
	
	<div class="row">
	
		<div class="col-md-12">

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            
		</div>
	
	</div>

    <?php ActiveForm::end(); ?>

</div>
