<?php

use common\helpers\WxPaymentHelper;
use function foo\func;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wx Unified Payment Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-unified-payment-order-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            [
                'attribute' => 'id',
                'value' => function ($data) {
                    return Html::a($data->id, ['view', 'id' => $data->id]);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'family_id',
                'label' => Yii::t('app', 'Family'),
                'value' => function ($data) {
                    if (!empty($data->family_id)) {
                        return Html::a($data->family->name, ['family/view', 'id' => $data->family_id]);
                    }
                    return '';
                },
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app', 'Program'),
                'value' => function ($data) {
                    if (!empty($data->price_id)) {
                        return Html::a($data->price->program->getNamei18n(), ['program/view', 'id' => $data->price->program_id]);
                    }
                    return '';
                },
                'format' => 'html'
            ],
            'total_fee:currency',
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return WxPaymentHelper::getStatusLabel($data->status);
                }
            ],
            'attach',
            'updated_at:datetime',
        ],
    ]); ?>
</div>
