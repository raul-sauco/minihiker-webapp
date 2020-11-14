<?php

use common\helpers\ClientHelper;
use common\helpers\FamilyHelper;
use common\models\Client;
use common\models\Program;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Client */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <p>

    	<?= Html::a(
    	       Yii::t('app', 'Add family member'),
    	       ['client/create', 'family_id' => $model->family_id],
    	       ['class' => 'btn btn-success'])?>

        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('app', 'Serial　Number'),
                'value' => FamilyHelper::getFormattedSerial($model->family),

            ],
            'name_zh',
            [
                'label' => Yii::t('app', 'Client Category'),
                'visible' => !empty($model->family->category),
                'value' => $model->family->category,
            ],
            [
                'label' => Yii::t('app', 'Is Verify'),
                'visible' => $model->user !== null,
                'value' => static function ($data) {
                    if ($data->user !== null) {
                        return $data->user->is_verify ?
                            Yii::t('app', 'Verified') :
                            Yii::t('app', 'Not Verified');
                    }
                    // Non weapp users do not have an user account.
                    return '';
                },
            ],
            [
                'label' => Yii::t('app', 'Membership Date'),
                'visible' => !empty($model->family->membership_date),
                'value' => $model->family->membership_date,
                'format' => 'date',
            ],
            [
                'attribute' => 'nickname',
                'visible' => !empty($model->nickname),
            ],
            [
                'attribute' => 'is_male',
                'label' => Yii::t('app', 'Sex'),
                'value' => static function ($data) {
                    return $data->is_male?Yii::t('app', 'Male'):Yii::t('app', 'Female');
                }
            ],
            [
                'attribute' => 'birthdate',
                'visible' => !empty($model->birthdate),
                'format' => 'date',
            ],
            [
                'label' => Yii::t('app', 'Place Of Residence'),
                'visible' => !empty($model->family->place_of_residence),
                'value' => $model->family->place_of_residence,
            ],
            [
                'attribute' => 'name_pinyin',
                'visible' => !empty($model->name_pinyin),
            ],
            [
                'attribute' => 'name_en',
                'visible' => !empty($model->name_en),
            ],
            [
                'attribute' => 'id_card_number',
                'visible' => !empty($model->id_card_number),
            ],
            [
                'attribute' => 'family_id',
                'label' => Yii::t('app', 'Family'),
                'value' => static function ($data) {
                    return empty($data->family_id)?null:
                        Html::a($data->family->name , ['family/view', 'id' => $data->family_id]);
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('app', 'Family Role'),
                'value' => static function($model) {
                    return ClientHelper::getRole($model);
                }
            ],
            [
                'attribute' => 'passport_number',
                'visible' => !empty($model->passport_number),
            ],
            [
                'attribute' => 'passport_issue_date',
                'visible' => !empty($model->passport_issue_date),
                'format' => 'date',
            ],
            [
                'attribute' => 'passport_expire_date',
                'visible' => !empty($model->passport_expire_date),
                'format' => 'date',
            ],
            [
                'attribute' => 'passport_place_of_issue',
                'visible' => !empty($model->passport_place_of_issue),
            ],
            [
                'attribute' => 'passport_issuing_authority',
                'visible' => !empty($model->passport_issuing_authority),
            ],
            [
                'attribute' => 'passport_image',
                'visible' => !empty($model->passport_image),
                'value' => static function(Client $data) {
                    return Html::img('@imgUrl/c/p/' . $data->passport_image, [
                            'alt' => $data->name_zh . ' passport image'
                    ]);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'phone_number',
                'visible' => !empty($model->phone_number),
            ],
            [
                'attribute' => 'phone_number_2',
                'visible' => !empty($model->phone_number_2),
            ],
            [
                'attribute' => 'email',
                'visible' => !empty($model->email),
                'format' => 'email',
            ],
            [
                'attribute' => 'wechat_id',
                'visible' => !empty($model->wechat_id),
            ],
            [
                'attribute' => 'place_of_birth',
                'visible' => !empty($model->place_of_birth),
            ],
            [
                'attribute' => 'dietary_restrictions',
                'visible' => !empty($model->dietary_restrictions),
            ],
            [
                'attribute' => 'allergies',
                'visible' => !empty($model->allergies),
            ],
            [
                'attribute' => 'remarks',
                'visible' => !empty($model->remarks),
            ],
        ],
    ]) ?>

    <div class="location-program-list">
    <header><?= Yii::t('app', 'Programs')?></header>
    	<?php Pjax::begin(); ?>
    	<?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->getPrograms(),
                'sort' => [
                    'defaultOrder' => [
                        'start_date' => SORT_DESC
                    ]
                ]
            ]),
            'columns' => [
                // 'id',
                [
                    'label' => Yii::t('app', 'Program'),
                    'value' => static function (Program $data) {
                        return Html::a($data->getNamei18n(),
                            ['program/view', 'id' => $data->id]);
                    },
                    'format' => 'html'
                ],
                [
                    'label' => Yii::t('app', 'Total participants'),
                    'value' => static function (Program $data){
                        return $data->getXLParticipantCount();
                    }
                ],
                'start_date:date',
                'end_date:date',
            ],
        ]); ?>
    	<?php Pjax::end(); ?>
	</div>

</div>
