<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Financial details (Family)');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Financial'),
    'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'All Families');

?>
<div class="financial-family-index">

<?php Pjax::begin(); ?>    <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel, 
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'name',
            [
                'label' => Yii::t('app', 'Total amount paid'),
                'value' => function ($data) {
                    return $data->getPayments()->sum('amount');
                },
                'format' => 'currency',
            ],
            [
                'label' => Yii::t('app', 'Total amount due'),
                'value' => function ($data) {
                    return $data->getProgramFamilies()->sum('final_cost');
                },
                'format' => 'currency',
            ],
            [
                'label' => Yii::t('app', 'Balance'),
                'value' => function ($data) {
                    return $data->getPayments()->sum('amount') - 
                        $data->getProgramFamilies()->sum('final_cost');
                },
                'format' => 'currency', 
                'contentOptions' => function ($model , $key , $index , $column) {
                    $paid = $model->getPayments()->sum('amount');
                    $due = $model->getProgramFamilies()->sum('final_cost');
                    
                    return [
                        'class' => (($paid - $due) < 0)?'financial-negative-balance':'',
                    ];
                },
            ],

             [
                 'class' => 'yii\grid\ActionColumn',
                 'template' => '{view}',
                 'buttons' => [
                     'view' => function ($url , $model , $key) {
                         return Html::a(
                             \yii\bootstrap\Html::icon('glyphicon glyphicon-eye-open') , 
                             ['financial/family-view', 'id' => $model->id] ,[
                                 'title' => Yii::t('yii', 'View'),
                                 'aria-label' => Yii::t('yii', 'View'),
                                 'data' => ['pjax' => 0],
                             ]);
                     }
                 ],
             ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>