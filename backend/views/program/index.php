<?php

use common\helpers\ProgramHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Program;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProgramSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Programs');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="program-index">

    <p>
        <?= Html::a(
                Yii::t('app', 'Create Program'),
            ['create'],
            ['class' => 'btn btn-success']) ?>
    </p>


    <?php Pjax::begin(); ?>

    <?= $this->render('_search', [
            'model' => $searchModel,
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'rowOptions' => function (Program $model) {
            return ['class' => ProgramHelper::getProgramIndexGridItemHighlightClass($model)];
        },
        'columns' => [
            [
                'attribute' => 'programGroup.name',
                'label' => Yii::t('app', 'Display name'),
                'value' => function (Program $data) {
                    return Html::a($data->getNamei18n(), ['view', 'id' => $data->id]);
                },
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app', 'Participant Count'),
                'value' => function (Program $data) {
                    // $f = $data->getFamilies()->count() . '家庭. ';
                    return $data->getXLParticipantCount();
                }
            ],
            /*
             * Kat recommends not using this format, dates in two different fields
             * seems more clear.
             * [
                'attribute' => 'start_date',
                'value' => function (Program $data) {
                    return $data->getDates();
                }
            ],*/
            'start_date:date',
            'updated_at:date'
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
