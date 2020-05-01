<?php 

use yii\widgets\ListView;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Search');
$this->params['breadcrumbs'][] = $this->title;

?>

<div id="search-index">

	<?= Html::tag('header', 
	    Yii::t('app', 'Displaying results for {query}', 
	        ['query' => Html::tag('span', 
	            Html::encode(Yii::$app->request->get('query')), 
	            ['class' => 'search-query-string'])]))?>

    <?= $this->render('_form') ?>
    
    <?= ListView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $results,
        ]),
        'itemView' => '_result',
        'itemOptions' => ['class' => 'search-result'],
    ]); ?>

</div>