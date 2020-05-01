<?php

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Location',
]) . $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Locations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_zh, 'url' => ['view', 'id' => $model->name_zh]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="location-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
