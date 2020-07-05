<?php

use common\helpers\ProgramHelper;
use common\models\User;
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
        'rowOptions' => static function (Program $model) {
            return ['class' => ProgramHelper::getProgramIndexGridItemHighlightClass($model)];
        },
        'columns' => [
            [
                'attribute' => 'programGroup.name',
                'label' => Yii::t('app', 'Display name'),
                'value' => static function (Program $data) {
                    return Html::a($data->getNamei18n(), ['view', 'id' => $data->id]);
                },
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app', 'Participant Count'),
                'value' => static function (Program $data) {
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
            [
                'label' => Yii::t('app', 'Created By'),
                'value' => static function (Program $program) {
                /** @var User $user */
                    if (($user = $program->createdBy) !== null) {
                        return $user->username;
                    }
                    return Yii::t('', 'N/A');
                }
            ],
            'updated_at:date',
            [
                'label' => Yii::t('app', 'Updated By'),
                'value' => static function (Program $program) {
                    /** @var User $user */
                    if (($user = $program->updatedBy) !== null) {
                        return $user->username;
                    }
                    return Yii::t('', 'N/A');
                }
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
