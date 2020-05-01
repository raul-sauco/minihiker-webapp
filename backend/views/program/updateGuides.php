<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = Yii::t('app', 'Update guides {programName}', [
    'programName' => $model->programGroup->name,
]);

$this->params['breadcrumbs'][] = [
        'label' => Yii::t('app', 'Programs'),
        'url' => ['index']
    ];

$this->params['breadcrumbs'][] = [
        'label' => $model->programGroup->name,
        'url' => ['view', 'id' => $model->id]
    ];

$this->params['breadcrumbs'][] = Yii::t('app', 'Update guides');

?>

<div class="program-update-guides">
	<?php $form = ActiveForm::begin()?>
	
	<?= $form->field($model, 'guides')->checkboxList(
	   User::find()->select(['username','id'])
           ->where(['or',
               ['user_type' => User::TYPE_ADMIN],
               ['user_type' => User::TYPE_USER]])
	       ->orderBy(['username' => SORT_ASC])
	       ->indexBy('id')
	       ->column(), 
	    []    
	)?>
	
	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary'])?>
	</div>
	
	<?php ActiveForm::end() ?>
</div>