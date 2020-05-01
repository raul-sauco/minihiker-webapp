<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\ProgramHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramFamily */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-family-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-lg-4">

            <?= $form->field($model, 'cost')->textInput([
                'oninput' => 'updateFinalCost();'
            ]) ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($model, 'discount')->textInput([
                'oninput' => 'updateFinalCost();'
            ]) ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($model, 'final_cost', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'disabled' => true,
                ],
            ])->textInput() ?>

        </div>

    </div>

    <div class="row">

    	<div class="col-lg-4">

    		<?= $form->field($model, 'status')->dropDownList(
        		    ProgramHelper::getStatus(),[
        		    'prompt' => Yii::t('app', 'Select Status'),
        		]) ?>

            <div class="action-buttons form-group">

                <?= Html::a(Yii::t('app', 'Back'),
                    Yii::$app->request->get('ref') === 'family' ?
                        ['financial/family-view', 'id' => $model->family_id ] :
                        ['program/view', 'id' => $model->program_id] ,
                    ['class' => 'btn btn-default']) ?>

                <?= Html::submitButton($model->isNewRecord ?
                    Yii::t('app', 'Create') :
                    Yii::t('app', 'Update'),
                    ['class' => $model->isNewRecord ?
                        'btn btn-success' : 'btn btn-primary']) ?>

            </div>

    	</div>

    	<div class="col-lg-8">

    		<?= $form->field($model, 'remarks')->textarea(['rows' => 3]) ?>

    	</div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
