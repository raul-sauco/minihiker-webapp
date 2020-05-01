<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Home');
?>

<div class="site-index">
    
	<?= Html::img(Yii::$app->urlManager->baseUrl . '/img/logo-sm.jpg', [
	    'class' => ''
	])?>

	<?= $this->render('/search/_form') ?>
		
	<p><?= Yii::t('app', 'Who are our clients?')?></p>
	
	<p><?= Yii::t('app', 'Where do they come from?')?></p>
	
	<p><?= Yii::t('app', 'Where do they want to go?')?></p>
    
</div>
