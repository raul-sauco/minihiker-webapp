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
            'created_at:relativeTime',
            'extra:ntext',
            'level',
            'page',
            'method',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
