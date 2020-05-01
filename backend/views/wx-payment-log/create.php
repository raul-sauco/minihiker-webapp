<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WxPaymentLog */

$this->title = Yii::t('app', 'Create Wx Payment Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wx Payment Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-payment-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
