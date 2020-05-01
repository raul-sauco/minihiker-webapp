<?php

use yii\bootstrap\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $selectAll boolean */
/* @var $queryString string */
/* @var $birthdate_before string */
/* @var $birthdate_after string */

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get'
]);
?>

<div class="client-search row">

	<div class="col-lg-10">
		
		<div class="input-group" id="client-index-search-box">
		
			<input type="search" class="form-control" name="queryString"
				aria-label="<?= Yii::t('app', 'Client Search Box')?>"
				placeholder="<?= Yii::t('app', 'Enter search text here') . '. ' . 
				    ($selectAll ? Yii::t('app', 'To see only kids uncheck the box to the right') : 
					   Yii::t('app', 'To see all clients check the box to the right')) ?>" 
				value="<?= $queryString ?? '' ?>">
			
			<span class="input-group-addon"> <input type="checkbox" name="selectAll"
				aria-label="<?= Yii::t('app', 'Client Search Filter')?>" 
				<?= $selectAll ? 'checked="checked"' : '' ; ?> title="Select only kids">
			</span>
			
		</div>
		
	</div>
	
	<div class="col-lg-2">

    	<?= Html::submitButton(Yii::t('app', 'Search'),
            ['class' => 'btn btn-primary']) ?>
    	
    </div>

</div>

<div class="row">

    <div class="col-lg-6">

        <div class="form-group">

            <?= Html::label(
                    Yii::t('app', 'Birthdate After'),
                    'birthdate_after'
            ) ?>

            <?= DatePicker::widget([
                'name' => 'birthdate_after',
                'dateFormat' => 'yyyy-MM-dd',
                'value' => $birthdate_after ?? '',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'defaultDate' => date('Y-m-d', strtotime('-18 years', time())),
                ],
                'options' => ['class' => 'form-control']
            ]) ?>

        </div>

    </div>

    <div class="col-lg-6">

        <div class="form-group">

            <?= Html::label(
                Yii::t('app', 'Birthdate Before'),
                'birthdate_after'
            ) ?>

            <?= DatePicker::widget([
                'name' => 'birthdate_before',
                'dateFormat' => 'yyyy-MM-dd',
                'value' => $birthdate_before ?? '',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'defaultDate' => date('Y-m-d', strtotime('-10 years', time())),
                ],
                'options' => ['class' => 'form-control']
            ]) ?>

        </div>

    </div>

</div>

</div>

<?php ActiveForm::end(); ?>
