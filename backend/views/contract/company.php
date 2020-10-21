<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Contract */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Registered Company');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contracts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-create">

	<h1><?= Html::encode($this->title) ?></h1>


	<div class="contract-form">

		<?php $form = ActiveForm::begin(['options' => ['id' => 'company']]); ?>

		<?= $form->field($model, 'companyname')->textInput() ?>
		<?= $form->field($model, 'certifytype')->dropDownList(['1'=>'社会信用代码','2'=>'营业执照注册号','3'=>'组织机构代码']) ?>
		<?= $form->field($model, 'certifynum')->textInput() ?>
		<?= $form->field($model, 'phoneno')->textInput() ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>