<?php

use common\helpers\ClientHelper;
use common\helpers\FamilyHelper;
use common\models\Client;
use common\models\Family;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Family */
/* @var $programFamilyDataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Families'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="family-view">

	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <p>
    
    	<?= Html::a(Yii::t('app', 'Add member'), 
    	    ['client/create', 'family_id' => $model->id], 
    	    ['class' => 'btn btn-success'])?>
    	
    	<?= Html::a(Yii::t('app', 'Financial info'), 
    	       ['financial/family-view', 'id' => $model->id],
    	       ['class' => 'btn btn-primary'])?>
    	       
        <?= Html::a(Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary'])
        ?>

        <?= Html::a(
                Yii::t('app', 'Search duplicates'),
                ['merge-search', 'id' => $model->id],
                ['class' => 'btn btn-primary']
        ) ?>
        
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
            'phone',
            'wechat',
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
                    return ClientHelper::getRole($data);
                },
            ],
            'phone_number',
        ],
    ]); ?>
    
    <?= Html::tag('header', Yii::t('app', 'Programs'))?>
    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $programFamilyDataProvider,
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
    ]); ?><?php Pjax::end();?>

</div>
