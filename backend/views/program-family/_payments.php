<?php

use common\models\Payment;
use yii\bootstrap\Html;

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

echo \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
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
            'value' => function (Payment $data) {
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
        'remarks'
    ],
]);