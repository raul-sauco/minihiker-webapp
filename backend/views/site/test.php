<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = 'Tests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-tests">
    <h2><?= Html::encode($this->title) ?></h2>
    <?= $this->render('tests/_permissions') ?>
</div>
