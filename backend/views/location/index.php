<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Locations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Location'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'name_zh',
                    'value' => function ($data) {
                        return Html::a(Html::encode($data->name_zh),
                            ['view', 'id' => $data->name_zh],
                            ['data' => ['pjax' => 0]]);
                    },
                    'format' => 'raw',
                ],
                [
                    'label' => Yii::t('app', 'Total Programs'),
                    'value' => function ($data) {
                        return $data->getPrograms()->count();
                    }
                ],
                [
                    'label' => Yii::t('app', 'Total Clients'),
                    'value' => function (\common\models\Location $data) {
                        return $data->getClientCount();
                    }
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>

</div>
