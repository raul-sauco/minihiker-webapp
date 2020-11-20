<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Weapp Logs');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="weapp-log-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'message',
            'created_at:relativeTime',
            'level',
            'page',
            'method',
            'extra:ntext',
            ['class' => ActionColumn::class],
        ],
    ]) ?>
    <?php Pjax::end(); ?>
</div>
