<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Wallet;

/* @var $this yii\web\View */
/* @var $model common\models\Expense */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['id' => 'expense-form']]); ?>

<?= Html::activeHiddenInput($model, 'family_id') ?>

<div class="row">

	<div class="col-md-4">
	
		<?= $form->field($model, 'amount')->textInput() ?>
	
	</div> 

	<div class="col-md-4">
	
		<?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
		    'dateFormat' => 'yyyy-MM-dd',
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
            ],
		    'options' => ['class' => 'form-control'],
		])?>
		    	
	</div>

	<div class="col-md-4">

		<?= $form->field($model, 'wallet_type')->dropDownList(Wallet::getWalletTypes()) ?>

	</div>

</div>

<div class="row">

	<div class="col-md-12">
	
		<?= $form->field($model, 'remarks')->textarea(['rows' => 3]) ?>
	
	</div>  

</div>

<div class="row">

	<div class="col-md-12">
        	
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>
	
	</div>  

</div>

<?php ActiveForm::end(); ?>

