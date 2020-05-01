<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WxPaymentLog */

$this->title = Yii::t('app', 'Update Wx Payment Log: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wx Payment Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="wx-payment-log-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
