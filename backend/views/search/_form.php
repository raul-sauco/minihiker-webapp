<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */

?>

<div id="search-form-container">

    <?= Html::beginForm(
        ['/search'],     // action
        'get',          // method
        [
            'id' => 'search-form',
            
        ]); ?>
    
    <div class="input-group">
    
    	<?= Html::textInput('query', null , [
            'id' => 'search-field',
    	    'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Search for...'),
        ]);?>
        
        <span class="input-group-btn " >
        
        	<?= Html::submitButton(Html::icon('search'), ['class' => 'btn btn-success'])?>
        
        </span>
    
    </div>    
    
    
    <?= Html::endForm(); ?>
    
</div>