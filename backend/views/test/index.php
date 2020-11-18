<?php

/* @var $this yii\web\View */

$this->title = 'Tests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-tests">
    <?= $this->render('_permissions') ?>
    <?= $this->render('_program-family') ?>
    <?= $this->render('_program-client') ?>
</div>
