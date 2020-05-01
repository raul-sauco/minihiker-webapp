<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Family;
use common\helpers\FamilyHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\FamilySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Families');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="family-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Family'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'value' => function (Family $data) {
                    return FamilyHelper::getFormattedSerial($data);
                }
            ],
            [
                'attribute' => 'name',
                'value' => function ($data) {
                    return Html::a(
                        Html::encode($data->name ?? ''),
                        ['view', 'id' => $data->id],
                        ['data' => ['pjax' => 0]]
                    );
                },
                'format' => 'raw',
            ],
            'address:ntext',
            'remarks:ntext',
        ],
    ]); ?>

    <?php Pjax::end(); ?></div>
