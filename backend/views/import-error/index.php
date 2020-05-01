<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Import Errors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-error-index">

<?php //Pjax::begin(); ?>    
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'message:ntext',
            'field',
            'field_value:ntext',
            'validation_errors:ntext',
            [
                'attribute' => 'client_id',
                'value' => function ($data) {
                    if ($data->client_id == null) {
                        return '';
                    } else {
                        return Html::a($data->client_id, ['client/view', 'id' => $data->client_id]);
                    }
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'family_id',
                'value' => function ($data) {
                    if ($data->family_id == null) {
                        return '';
                    } else {
                        return Html::a($data->family_id, ['family/view' , 'id' => $data->family_id]);
                    }
                },
                'format' => 'html',
            ],
            // 'family_id',
            // 'created_by',
            // 'updated_by',
            // 'created_at',
            // 'updated_at',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php //Pjax::end(); ?></div>
