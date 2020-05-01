<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Locations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-view">

	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->name_zh], ['class' => 'btn btn-primary']) ?>
        <?php
        if ($model->getPrograms()->count() == 0) {
            echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->name_zh], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);     
        }?>
    </p>
    
    <header><?= Yii::t('app', 'Programs in this location')?></header>
    
    <div class="location-program-list">
    	<?php Pjax::begin(); ?>    
    	<?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->getPrograms()
                    ->orderBy('start_date DESC'),
            ]),
            'columns' => [
                // 'id',
                [
                    'label' => Yii::t('app', 'Program'),
                    'value' => function ($data) {
                        return Html::a($data->getNamei18n(),
                            [
                                'program/view', 
                                'id' => $data->id,
                            ],
                            [
                                'data' => ['pjax' => 0]
                            ]);
                    },
                    'format' => 'raw',
                ],
                [
                    'label' => Yii::t('app', 'Total participants'),
                    'value' => function ($data){
                        return $data->getLongParticipantCount();
                    }
                ],
                [
                    'attribute' => 'start_date',
                    'format' => 'date',
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'end_date',
                    'format' => 'date',
                    'enableSorting' => false,                    
                ],
            ],
        ]); ?>
    	<?php Pjax::end(); ?>
	</div>

</div>
