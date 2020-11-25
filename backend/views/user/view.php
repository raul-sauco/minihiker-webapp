<?php

use common\helpers\UserHelper;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Users'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>
    <p>
        <?= Html::a(Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>
        <?php
        if ($model->user_type !== User::TYPE_SUSPENDED &&
            Yii::$app->user->can('deleteUser')) {
            echo Html::a(Yii::t('app', 'Suspend'),
                ['suspend', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app',
                        'Are you sure you want to suspend this user?'),
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            // 'avatar',
            'name_zh',
            'name_pinyin',
            'name_en',
            'birthdate:date',
        	[
                'attribute' => 'is_male',
                'label' => Yii::t('app', 'Gender'),
                'value' => static function ($model) {
                        return $model->is_male ?
                            Yii::t('app', 'Male'):
                            Yii::t('app', 'Female');
                }
    		],
            'id_card_number',
            'passport_number',
            'passport_issue_date:date',
            'passport_expire_date:date',
            'passport_place_of_issue',
            'passport_issuing_authority',
            'place_of_birth',
            [
                'attribute' => 'user_type',
                'value' => static function ($data) {
                    return UserHelper::getUserTypeLabel($data->user_type);
                }
            ],
        ],
    ]) ?>
</div>
