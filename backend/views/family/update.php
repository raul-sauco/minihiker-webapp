<?php

/* @var $this yii\web\View */
/* @var $model common\models\Family */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Family',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Families'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="family-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
