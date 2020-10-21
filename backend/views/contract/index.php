<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t('app', 'Contract');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-index">

    <!--<h1><? /*= Html::encode($this->title) */ ?></h1>-->

    <p>
		<?=Html::a(Yii::t('app', 'Create Contract'), ['create'], ['class' => 'btn btn-success'])?>
    </p>

	<?php echo $this->render('_search', ['model' => $searchModel]); ?>

	<?=GridView::widget([
		'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
		'columns'      => [
			//['class' => 'yii\grid\SerialColumn'],

			// 'id',
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
				'value'     => function ($data) {
					return \common\models\Contract::getAttrStatus($data->status);
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
