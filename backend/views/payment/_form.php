<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Wallet;

/* @var $this yii\web\View */
/* @var $model common\models\Payment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'payment-form']]); ?>
    
    <div class="row">

    	<div class="col-lg-4">

    		<?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
    		    'dateFormat' => 'yyyy-MM-dd',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                ],
    		    'options' => ['class' => 'form-control'],
			]) ?>
    		
    	</div>

    	<div class="col-lg-4">
    		<?= $form->field($model, 'amount')->textInput() ?>
    		
    	</div>

        <div class="col-lg-4">

            <?php
            if (empty($model->program_id)) {

                echo $form->field($model, 'wallet_type')
                    ->dropDownList(Wallet::getWalletTypes());

            } else {

                $label = Html::label(
                        Yii::t('app', 'Program'),
                        'payment-form-program-name',
                        ['class' => 'control-label']);

                $input = Html::textInput('program-name',
                    $model->program->getNamei18n(),
                    [
                        'disabled' => true,
                        'class' => 'form-control',
                        'id' => 'payment-form-program-name'
                    ]);

                echo Html::tag('div',
                    $label . $input,
                    ['class' => 'form-group']);

            }
            ?>

        </div>

    </div>


    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ?
            Yii::t('app', 'Create') :
            Yii::t('app', 'Update'),
                ['class' => $model->isNewRecord ?
                    'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
