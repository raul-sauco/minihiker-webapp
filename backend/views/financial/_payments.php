<?php

use yii\bootstrap\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use common\models\Wallet;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $family common\models\Family */

// Find payments
$paymentDataProvider = new ActiveDataProvider([
    'query' => $family->getPayments(),
    'sort' => [
        'defaultOrder' => ['date' => SORT_DESC],
    ],
]);
?>

<header>

    <?= Yii::t('app', 'Payment Details') ?>

    <?= Html::a(
        ' ' . \yii\bootstrap\Html::icon('glyphicon glyphicon-plus-sign'),
        ['payment/create' , 'family_id' => $family->id],
        [
            'title' => Yii::t('app', 'Add Payment'),
            'id' => 'financial-family-view-add-payment'
        ]
    ) ?>

</header>

<?php
Pjax::begin();

echo GridView::widget([
    'dataProvider' => $paymentDataProvider,
    // 'filterModel' => $searchModel,
    'columns' => [
        'date:date',
        'amount:currency',
        [
            'attribute' => 'program_id',
            'value' => function (\common\models\Payment $data) {
                return $data->program_id !== null ? $data->program->getNamei18n() : '' ;
            }
        ],
        [
            'attribute' => 'wallet_type',
            'label' => Yii::t('app', 'Wallet Type'),
            'value' => function ($data) {
                return $data->wallet_type != null ?
                    Wallet::getWalletTypes()[$data->wallet_type] : '' ;
            }
        ],
        'remarks',
        [
            'value' => function ($data) {

                return Html::a(
                    Yii::t('app', 'Update'),
                    [
                        'payment/update', 'id' => $data->id,
                        'ref' => 'financial',
                    ],
                    [
                        'class' => 'btn btn-sm btn-primary'
                    ]
                ) . ' ' .
                    Html::a(
                        Yii::t('app', 'Delete'),
                        [
                            'payment/delete', 'id' => $data->id,
                            'ref' => 'financial',
                        ],
                        [
                            'class' => 'btn btn-sm btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app',
                                    'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ]
                        ]
                    );

            },
            'format' => 'raw'
        ],
    ],
]);

Pjax::end();
?>