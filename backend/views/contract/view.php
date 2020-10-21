<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Contract */

$this->title = $model->contracttitle;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contracts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contract-view">

   <!-- <h1><?/*= Html::encode($this->title) */?></h1>-->

    <p>
       <!-- --><?/*= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) */?>
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
           /* 'id',*/
            'contractno',
            'contractfile',
            'contracttitle',
            'touid',
	        [
		        'attribute' => 'status',
		        'value' => function ($data) {
			        return \common\models\Contract::getAttrStatus($data->status);
		        },
	        ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
