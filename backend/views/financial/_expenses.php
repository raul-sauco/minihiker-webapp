<?php

use yii\bootstrap\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use common\models\Wallet;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $family common\models\Family */

// Find expenses
$expenseDataProvider = new ActiveDataProvider([
    'query' => $family->getExpenses(),
    'sort' => [
        'defaultOrder' => ['date' => SORT_DESC],
    ],
]);

?>

<header>
    <?= Yii::t('app', 'Expense Details') ?>
    <?= Html::a(
        ' ' . \yii\bootstrap\Html::icon('glyphicon glyphicon-plus-sign'),
        ['expense/create' , 'family_id' => $family->id],
        [
            'title' => Yii::t('app', 'Add Expense'),
            'id' => 'financial-family-view-add-expense'
        ]
    )?>
</header>

<?php
Pjax::begin();

echo GridView::widget([
    'dataProvider' => $expenseDataProvider,
    'columns' => [
        'date:date',
        'amount:currency',
        [
            'attribute' => 'wallet_type',
            'label' => Yii::t('app', 'Wallet type'),
            'value' => function ($data) {
                return Wallet::getWalletTypes()[$data->wallet_type];
            }
        ],
        'remarks',

        [
            'value' => function ($data) {

                $updateLink = Html::a(
                        Yii::t('app', 'Update'),
                        ['expense/update', 'id' => $data->id],
                        ['class' => 'btn btn-sm btn-primary']
                );

                $deleteLink = Html::a(
                        Yii::t('app', 'Delete'),
                        ['expense/delete', 'id' => $data->id],
                        [
                            'class' => 'btn btn-sm btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app',
                                    'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ]
                        ]
                );

                return $updateLink . ' ' . $deleteLink;

            },
            'format' => 'raw',
        ],
    ],
]);

Pjax::end();
?>
