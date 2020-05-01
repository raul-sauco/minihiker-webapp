<?php

use common\helpers\ClientHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(['id' => 'client-form']); ?>
    
    <div class="row">
    
    	<div class="col-lg-4">
    		
    		<?= Html::activeHiddenInput($model, 'family_id') ?>

            <?= $form->field($model, 'name_zh')->textInput(
                [
                    'maxlength' => true, 
                    'data' => ['url' => Url::toRoute(['client/check-unique-zh-name'])]
                ]) ?>
        
            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($model, 'name_pinyin')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
    	
    	</div>
    
    	<div class="col-lg-4">

            <?= $form->field($model, 'family_role_id')->dropDownList(
                    ClientHelper::getClientRolesDropdown(), [
                        'prompt' => Yii::t('app', 'Select One'),
                        'id' => 'family-role-dropdown'
                ]
            ) ?>
        	
   			<?= $form->field($model, 'family_role_other', [
   			        'options' => [
                        'id' => 'family-role-other-container',
                        'class' => $model->family_role_id === 1 ? '' : 'hidden-field'
                    ]
                ])->textInput(['maxlength' => true])
            ?>
   
            <?= $form->field($model, 'birthdate', ['options' => ['class' => 'form-group']])
                ->widget(DatePicker::class, [
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                    'options' => ['class' => 'form-control']
        	]) ?>

    		<?= $form->field($model, 'place_of_birth')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($model, 'is_male')->dropDownList(
                [
                    0 => Yii::t('app', 'Female'),
                    1 => Yii::t('app', 'Male')
                ],[
                    'prompt' => Yii::t('app', 'Sex'),
                ]) ?>
    	
    	</div>
    
    	<div class="col-lg-4">
        
            <?= $form->field($model, 'passport_issue_date', ['options' => ['class' => 'form-group']])
                ->widget(DatePicker::class, [
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                    'options' => ['class' => 'form-control']
            ]) ?>
        
            <?= $form->field($model, 'passport_expire_date', ['options' => ['class' => 'form-group']])
                ->widget(DatePicker::class, [
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                    'options' => ['class' => 'form-control']
            ]) ?>
        
            <?= $form->field($model, 'passport_place_of_issue')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($model, 'passport_issuing_authority')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'passport_image')->textInput(['maxlength' => true]) ?>
    	
    	</div>
    
    </div>
    
    <div class="row">
    
    	<div class="col-lg-6">

            <?= $form->field($model, 'allergies')->textarea(['rows' => 2]) ?>

            <?= $form->field($model, 'dietary_restrictions')->textarea(['rows' => 2]) ?>


        </div>
    
    	<div class="col-lg-6">
        
            <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>    		
    	
    	</div>
    
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
