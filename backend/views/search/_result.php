<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */

?>

<div class="search-result-header">

	<?= Html::a($model->header, $model->link, 
	    ['title' => Yii::t('app', 'Search Rank {searchRank}', 
	        ['searchRank' => $model->searchRank])]) ?>
	
	<?= Html::tag('span', '(' . $model->model . ')', 
	    ['class' => 'search-result-model']) ?>

</div>

<div class="search-result-sub-header">

	<?= $model->subHeader ?>

</div>

<div class="search-result-body">

	<?= $model->body ?>

</div>