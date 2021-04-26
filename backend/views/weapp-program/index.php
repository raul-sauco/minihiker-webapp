<?php

use common\helpers\ProgramHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use common\models\Program;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProgramSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $q string */

$this->title = Yii::t('app', 'Programs');
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="weapp-program-index">

    <?php Pjax::begin(); ?>

    <?= $this->render('_search', [
        'model' => $searchModel,
        'q' => $q
    ]) ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_item',
        'options' => ['class' => 'list-view'],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
