<?php

/* @var $this yii\web\View */
/* @var $model common\models\Supplier */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Supplier',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Suppliers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="supplier-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
