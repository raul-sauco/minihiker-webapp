<?php

use common\models\ProgramGuide;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$guideDataProvider = new ActiveDataProvider([
    'query' => $model->getGuides(),
]);

echo GridView::widget([
    'dataProvider' => $guideDataProvider,
    'columns' => [
        [
            'label' => Yii::t('app', 'Guide Name'),
            'attribute' => 'username',
        ],
        [
            'label' => Yii::t('app', 'Remarks'),
            'value' => static function (User $data) use ($model) {
                $pg = ProgramGuide::findOne([
                    'program_id' => $model->id,
                    'user_id' => $data->id]
                );
                return $pg->notes ?? '';
            }
        ],
    ],
]);
