<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $companyed */

$this->title                   = Yii::t('app', 'Contract');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-index">

    <p>
        <?php if ($companyed):?>
		   <?=Html::a(Yii::t('app', 'Create Contract'), ['create'], ['class' => 'btn btn-success'])?>
	        <?=Html::a(Yii::t('app', 'Registered Company'), ['company'], ['class' => 'btn btn-primary'])?>
        <?php else:?>
	        <?=Html::a(Yii::t('app', 'Registered Company'), ['company'], ['class' => 'btn btn-primary'])?>
        <?php endif;?>
    </p>

	<?php echo $this->render('_search', ['model' => $searchModel]); ?>

	<?=GridView::widget([
		'dataProvider' => $dataProvider,
		'columns'      => [
			'contractno',
			'contractfile',
			'contracttitle',
			[
				'attribute' =>'touid',
				'value' => function ($data) {
					return $data->uname->name_zh ?? null;
				}
			],
			[
				'attribute' => 'status',
				'format'    => 'raw',
				'value'     => function ($data) {
					if ($data->user_status == 1 && $data->admin_status == 0) {
						$textColor = 'text-primary';
					} elseif ($data->user_status == 0 && $data->admin_status == 1) {
						$textColor = 'text-success';
					} elseif ($data->status == 1) {
						$textColor = 'text-warning';
					} elseif ($data->status == 2) {
						$textColor = 'text-danger';
					} else {
						$textColor = 'text-muted';
					}

					return "<p class='{$textColor}'>" . \common\models\Contract::getAttrStatus($data) . "</p>";
				},
			],
			'created_at:datetime',
			'updated_at:datetime',

			[
				'class'    => 'yii\grid\ActionColumn',
				'template' => '{send} {query} {delete}',
				'buttons'  => [
					'send' => function ($url, $model, $key) {
						$options = [
							'title'      => Yii::t('yii', 'Send'),
							'aria-label' => Yii::t('yii', 'Send'),
							'data-pjax'  => '0',
						];
						if($model->status>=1){
						    return false;
                        }
						return Html::a('<span class="glyphicon glyphicon-send"></span>', $url, $options);
					},
                    'query'=>function($url,$model,$key){
	                    $options = [
		                    'title'      => Yii::t('yii', 'View'),
		                    'aria-label' => Yii::t('yii', 'View'),
		                    'data-pjax'  => '0',
	                    ];
	                    if($model->status<1){
		                    return false;
	                    }
	                    return Html::a('<span class="glyphicon glyphicon-folder-open"></span>', $url, $options);
                    }
				]
			],
		],
	]);?>


</div>
