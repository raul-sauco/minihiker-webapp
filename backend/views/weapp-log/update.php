<?php

/* @var $this yii\web\View */
/* @var $model common\models\WeappLog */

$this->title = Yii::t('app', 'Update Weapp Log: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Weapp Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="weapp-log-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
