<?php

/* @var $this yii\web\View */
/* @var $model common\models\Expense */

$this->title = Yii::t('app', 'Update Expense: {nameAttribute}', [
    'nameAttribute' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="expense-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
