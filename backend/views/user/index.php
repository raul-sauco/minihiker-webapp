<?php

use yii\grid\ActionColumn;
use common\helpers\UserHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $q string */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?php
    Pjax::begin();
    echo $this->render('_search', [
            'q' => $q
        ]);
    echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'username',
                'name_zh',
                'name_en',
                [
                    'attribute' => 'user_type',
                    'value' => static function ($data) {
                        return UserHelper::getUserTypeLabel($data->user_type);
                    },
                    'visible' => Yii::$app->user->can('listUsers')
                ],
                ['class' => ActionColumn::class],
            ],
        ]);
    Pjax::end();
    ?>
</div>
