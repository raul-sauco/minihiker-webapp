<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\helpers\ClientHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $selectAll boolean */
/* @var $queryString string */
/* @var $birthdate_before string */
/* @var $birthdate_after string */

$this->title = Yii::t('app', 'Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <?= $this->render('_search', [
        'queryString' => $queryString,
        'selectAll' => $selectAll,
        'birthdate_before' => $birthdate_before,
        'birthdate_after' => $birthdate_after
    ]) ?>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'label' => Yii::t('app', 'Name'),
                    'value' => function ($data) {
                        return Html::a(
                            Html::encode($data->name),
                            ['view', 'id' => $data->id],
                            ['data' => ['pjax' => 0]]);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'nickname',
                    'enableSorting' => false,
                    'value' => function ($data) {
    	                return !empty($data->nickname) ? Html::encode($data->nickname) : '' ;
                    }
                ],
                [
                    'attribute' => 'birthdate'
                ],
                [
                    'label' => Yii::t('app', 'Category'),
                    'value' => function ($data) {
                        return $data->family->category ?? null;
                    }
                ],
                [
                    'label' => Yii::t('app', 'Family'),
                    'value' => function ($data) {
                    return $data->family_id === null?'':
                        Html::a(
                            Html::encode($data->family->name),
                            ['family/view' , 'id' => $data->family_id],
                            ['data' => ['pjax' => 0]]);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'is_kid' ,
                    'label' => Yii::t('app', 'Family Role') ,
                    'value' => function ($data) {

                        return ClientHelper::getRole($data);

                    },
                    'enableSorting' => false,
                ],
            ],
        ]); ?>
	<?php Pjax::end(); ?></div>
