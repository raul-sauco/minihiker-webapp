<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Weapp Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="weapp-log-index">

    <p>
        <?= Html::a(Html::icon('plus'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'message',
            'extra:ntext',
            'level',
            'page',
            'method',
            //'line',
            //'timestamp',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
