<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wx Payment Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-payment-log-index">

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'id',
            'message',
            'raw:ntext',
            [
                'attribute' => 'updated_at',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDatetime($data->updated_at);
                }
            ],
            //'order',
            //'user',
            //'get:ntext',
            //'post:ntext',
            //'headers:ntext',
            //'method',
            //'notes:ntext',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
