<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WxPaymentLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wx Payment Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="wx-payment-log-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'message',
            'raw',
            [
                'attribute' => 'order',
                'visible' => !empty($model->order)
            ],
            [
                'attribute' => 'user',
                'visible' => !empty($model->user)
            ],
            [
                'attribute' => 'notes',
                'visible' => !empty($model->notes)
            ],
            [
                'attribute' => 'headers',
                'visible' => !empty($model->headers)
            ],
            [
                'attribute' => 'get',
                'visible' => !empty($model->get)
            ],
            [
                'attribute' => 'post',
                'visible' => !empty($model->post)
            ],
            [
                'attribute' => 'method',
                'visible' => !empty($model->method)
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime'
            ]
        ],
    ]) ?>

</div>
