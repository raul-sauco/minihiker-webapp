<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Contract */

$this->title = Yii::t('app', 'Update Contract: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contracts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->contracttitle, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contract-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
