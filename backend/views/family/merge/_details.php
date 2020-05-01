<?php

/* @var $this yii\web\View */
/* @var $model common\models\Family */

use common\helpers\FamilyHelper;
use common\models\Client;
use common\models\Family;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

?>


<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

<?= Html::tag('header', Yii::t('app', 'Details'))?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => Yii::t('app', 'Serial Number'),
            'attribute' => 'id',
            'value' => function (Family $data) {
                return FamilyHelper::getFormattedSerial($data);
            }
        ],
        'category',
        [
            'attribute' => 'membership_date',
            'format' => 'date',
        ],
        'name',
        'place_of_residence',
        'address:ntext',
        'remarks:ntext',
    ],
]) ?>

<?= Html::tag('header', Yii::t('app', 'Family members'))?>

<?= GridView::widget([
    'dataProvider' => new ActiveDataProvider(['query' => $model->getClients()]),
    'columns' => [
        [
            'attribute' => 'name_zh',
            'label' => Yii::t('app', 'Name'),
            'format' => 'raw',
            'enableSorting' => false,
            'value' => function (Client $data) {
                return Html::a(
                    Html::encode($data->getName()),
                    ['client/view' , 'id' => $data->id],
                    [
                        'data' => ['pjax' => 0] ,
                        'aria' => Yii::t('app', 'View client') ,
                        'title' => Yii::t('app', 'View client'),
                    ]
                );
            }
        ],
        [
            'label' => Yii::t('app', 'Family role'),
            'value' => function (Client $data) {

                if (Yii::$app->language === 'zh-CN' && !empty($data->familyRole->zh)) {
                    return $data->familyRole->zh;
                }

                return $data->familyRole->en ?? null;

            },
        ],
        'phone_number',
    ],
]); ?>

<?= Html::tag('header', Yii::t('app', 'Programs'))?>

<?= GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getProgramFamilies()
    ]),
    'columns' => [
        [
            'attribute' => 'program_id',
            'label' => Yii::t('app', 'Program'),
            'value' => function ($data) {
                return $data->program->getNamei18n();
            }
        ],
        'remarks',
        'cost',
        'discount',
        'final_cost',
        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'program-family',
            'template' => '{update}'
        ],

    ],
]);
?>

