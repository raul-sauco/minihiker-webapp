<?php

use common\models\Payment;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramFamily */

echo $this->render('_overview', ['model' => $model]);

$addPaymentUrl = [
    'payment/create',
    'program_id' => $model->program_id,
    'family_id' => $model->family_id,
];

if (Yii::$app->request->get('ref') === 'family') {

    $addPaymentUrl['ref'] = 'family';

}

$addLink = Html::a(
    Html::icon('plus-sign'),
    $addPaymentUrl);

echo Html::tag('header',
    Yii::t('app', 'Program Payments') . ' ' . $addLink);

echo GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->family->getPayments()
            ->where(['program_id' => $model->program_id]),
        'sort' => [
            'defaultOrder' => [
                'date' => SORT_DESC,
            ]
        ]
    ]),
    'columns' => [
        [
            'attribute' => 'program_id',
            'value' => static function (Payment $data) {
                return Html::a($data->program->getNamei18n(),
                    [
                        'payment/update', 'id' => $data->id,
                        'ref' => Yii::$app->request->get('ref'),
                    ]);
            },
            'format' => 'html',
        ],
        'amount:currency',
        'date:date',
        'remarks',
        [
            'label' => Yii::t('app', 'Edit'),
            'value' => static function (Payment $data) {
                return Html::a(
                        Html::icon('pencil') . ' ' .
                        Yii::t('app', 'Edit'),
                    [
                        'payment/update', 'id' => $data->id,
                        'ref' => Yii::$app->request->get('ref'),
                    ],
                    [
                        'class' => 'btn btn-sm btn-primary'
                    ]
                );
            },
            'format' => 'html',
        ]
    ]
]);
