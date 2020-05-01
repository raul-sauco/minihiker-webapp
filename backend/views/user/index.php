<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'username',
            // 'avatar',
            'name_zh',
            // 'name_pinyin',
            'name_en',
            [
                'attribute' => 'user_type',
                'value' => function ($data) {
                    return \common\helpers\UserHelper::getUserTypeLabel($data->user_type);
                },
                'visible' => Yii::$app->user->can('listUsers')
            ],
            // 'birthdate',
            // 'is_male:boolean',
            // 'id_card_number',
            // 'passport_number',
            // 'passport_issue_date',
            // 'passport_expire_date',
            // 'passport_place_of_issue',
            // 'passport_issuing_authority',
            // 'place_of_birth',
            // 'created_by',
            // 'updated_by',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
