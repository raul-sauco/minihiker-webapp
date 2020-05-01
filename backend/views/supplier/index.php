<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Supplier;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Suppliers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-index">

    <p>
        <?= Html::a(
                Yii::t('app', 'Create Supplier'),
                ['create'],
                ['class' => 'btn btn-success'])
        ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'value' => function (Supplier $data) {

                    $name = Html::tag('p',
                        Html::a(
                            Html::encode($data->name),
                            ['view', 'id' => $data->id]
                        ), ['class' => 'supplier-index-name']);

                    $address = empty($data->address) ? '' :
                        Html::tag('p', Html::encode($data->address),
                            ['class' => 'supplier-index-address']);

                    $remarks = empty($data->remarks) ? '' :
                        Html::tag('p', Html::encode($data->remarks),
                            ['class' => 'supplier-index-remarks']);

                    return $name . $address . $remarks;

                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('app', 'Contacts'),
                'value' => function (Supplier $data) {

                    return $this->render('_contacts', ['model' => $data]);
                },
                'format' => 'raw',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
