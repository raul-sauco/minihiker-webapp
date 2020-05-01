<?php

use common\helpers\ProgramHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramClient */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-client-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
    	<div class="col-lg-4">
    		<?= $form->field($model, 'status')->dropDownList(
        		    ProgramHelper::getStatus(),[
        		    'prompt' => Yii::t('app', 'Select Status'),
        		]) ?>
    	</div>
    	<div class="col-lg-8">

    		<?= $form->field($model, 'remarks')->textarea(['rows' => 4]) ?>

    	</div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ?
            Yii::t('app', 'Create') : Yii::t('app', 'Update'),
            ['class' => $model->isNewRecord ?
                'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
