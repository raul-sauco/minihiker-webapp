<?php

use common\helpers\WxPaymentHelper;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

echo Html::tag('p',
    Html::a(Yii::t('app', 'Fix it'),
        ['test/update-payment-order-status'],
        ['class' => 'btn btn-primary']
    )
);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'id',
            'value' => static function ($data) {
                return Html::a($data->id, ['view', 'id' => $data->id]);
            },
            'format' => 'html'
        ],
        [
            'attribute' => 'family_id',
            'label' => Yii::t('app', 'Family'),
            'value' => static function ($data) {
                if (!empty($data->family_id)) {
                    return Html::a($data->family->name,
                        ['family/view', 'id' => $data->family_id]);
                }
                return '';
            },
            'format' => 'html'
        ],
        [
            'label' => Yii::t('app', 'Program'),
            'value' => static function ($data) {
                if (!empty($data->price_id)) {
                    return Html::a($data->price->program->getNamei18n(),
                        ['program/view', 'id' => $data->price->program_id]);
                }
                return '';
            },
            'format' => 'html'
        ],
        'total_fee:currency',
        [
            'attribute' => 'status',
            'value' => static function ($data) {
                return WxPaymentHelper::getStatusLabel($data->status);
            }
        ],
        'attach',
        'updated_at:datetime',
    ]
]);
