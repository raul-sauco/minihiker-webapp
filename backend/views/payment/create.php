<?php

/* @var $this yii\web\View */
/* @var $model common\models\Payment */

$this->title = Yii::t('app',
    '{family} Family, Add Payment',
    ['family' => $model->family->name]);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Families'),
    'url' => ['family/index']];

$this->params['breadcrumbs'][] = [
    'label' => $model->family->name,
    'url' => ['family/view', 'id' => $model->family->id]];

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Financial Details'),
    'url' => ['financial/family-view', 'id' => $model->family->id]];

$this->params['breadcrumbs'][] = Yii::t('app', 'Add Payment');

// Set date to today by default
$model->date = date('Y-m-d');

?>

<div class="payment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
