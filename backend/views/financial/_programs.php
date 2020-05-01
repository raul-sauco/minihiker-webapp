<?php

use common\models\ProgramFamily;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $family common\models\Family */

$programDataProvider = new ActiveDataProvider([
    'query' => $family->getProgramFamilies()->orderBy('program_id DESC'),
]);

echo Html::tag('header', Yii::t('app', 'Program Details'));

echo GridView::widget([
    'dataProvider' => $programDataProvider,
    'columns' => [
        [
            // 'attribute' => 'program.namei18n',
            'label' => Yii::t('app', 'Name'),
            'value' => 'program.namei18n',
            'format' => 'html',
            'contentOptions' => [
                'class' => 'financial-family-index-programs-program-name'],
        ],
        [
            'attribute' => 'cost',
            'format' => 'currency',
            'enableSorting' => false,
            'contentOptions' => [
                'class' => 'financial-family-index-programs-cost'],

        ],
        [
            'attribute' => 'discount',
            'format' => 'currency',
            'enableSorting' => false,
            'contentOptions' => [
                'class' => 'financial-family-index-programs-discount'],

        ],
        [
            'attribute' => 'final_cost',
            'format' => 'currency',
            'enableSorting' => false,
            'contentOptions' => [
                'class' => 'financial-family-index-programs-final-cost'],

        ],
        [
            'attribute' => 'remarks',
            'enableSorting' => false,
            'contentOptions' => [
                'class' => 'financial-family-index-programs-remarks'],
        ],
        [
            'label' => Yii::t('app', 'Total Paid'),
            'value' => function (ProgramFamily $data) {

                $sum = $data->getPayments()->sum('amount');
                return $sum ?? 0;

            },
            'format' => 'currency',
            'contentOptions' => [
                'class' => 'financial-family-index-programs-paid'],
        ],
        [
            'label' => Yii::t('app', 'Balance'),
            'value' => function (ProgramFamily $data) {

                $balance = $data->getPayments()->sum('amount') - $data->final_cost;
                $cell = Html::tag('span',
                    Yii::$app->formatter->asCurrency($balance),
                    ['class' => ($balance < 0) ? 'financial-negative-balance' : '',]);

                return $cell;
            },
            'format' => 'html',
            'contentOptions' => [
                'class' => 'financial-family-index-programs-balance'],

        ],
        [
            'value' => function (ProgramFamily $data) {
                return Html::a(
                    Yii::t('app', 'Update'),
                    [
                        '/program-family/update',
                        'program_id' => $data->program_id,
                        'family_id' => $data->family_id,
                        'ref' => 'family'
                    ],
                    ['class' => 'btn btn-sm btn-primary']);
            },
            'format' => 'html',
        ]
    ],
    'options' => ['id' => 'financial-family-index-programs'],
]);