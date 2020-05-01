<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramPrice */

$this->title = Yii::t('app', 'Create Program Price');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Program Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-price-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
