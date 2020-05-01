<?php

use backend\assets\QaAsset;
use common\models\ProgramGroup;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model ProgramGroup */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Qas');
$this->params['breadcrumbs'][] = [
    'url' => ['/program'],
    'label' => Yii::t('app', 'Programs')
];
$this->params['breadcrumbs'][] = [
    'url' => ['/program/view', 'id' => $model->getPrograms()->one()->id],
    'label' => $model->weapp_display_name
];
$this->params['breadcrumbs'][] = $this->title;

QaAsset::register($this);
$this->registerJs('attachHandlers();');
?>
<div class="program-group-qas-index">

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_qaItem'
    ]); ?>

</div>

