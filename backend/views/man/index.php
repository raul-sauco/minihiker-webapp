<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Markdown;

$this->title = Yii::t('app', 'Manual');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-manual">
    
    <div class="row">
    
    	<div class="col-md-9">
    	
        	<h1><?= Html::encode($this->title) ?></h1>
        	
        	<p>
        		This page contains instructions on the basic functioning of 
        		the web application.
        	</p>

            <?= Markdown::process($this->render('_client')) ?>

            <?= Markdown::process($this->render('_family')) ?>
        
            <?= Markdown::process($this->render('_location')) ?>
            
            <?= Markdown::process($this->render('_program')) ?>

            <?= Markdown::process($this->render('_supplier')) ?>
        
        </div>
    
    	<div class="col-md-3" id="site-manual-sidebar">
        
            <?= Markdown::process($this->render('_toc')) ?>
        
        </div>
        
    </div>
    
</div>
