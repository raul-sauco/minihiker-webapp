<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model common\models\Contract */

$this->title = Yii::t('app', 'Contract');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->contracttitle, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'User Verify List');
?>
<div class="contract-index">

	<?php  echo $this->render('_send_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
		'columns' => [
			//['class' => 'yii\grid\SerialColumn'],
			'username',
			'name_zh',
            'id_card_number',
			'phone_number',
            //'is_verify',
			['class' => 'yii\grid\ActionColumn',
			 'template'=>'{send}',
			 'buttons'=>[
				 'send'=>function($url,$model,$key){
					 $url=$url.'&cid='.Yii::$app->request->getQueryParam('id');
					 $options = [
						 'title' => Yii::t('yii', 'View'),
						 'aria-label' => Yii::t('yii', 'View'),
						 'data-pjax' =>0,
                         'data-method'=>'post',
                         'data-confirm'=>'是否确认发送？'
					 ];
					 return Html::a('<span class="glyphicon glyphicon-send"></span>', $url, $options);
				 }
			 ]
			],
		],
       // 'pjax'=>true
	]); ?>


</div>
