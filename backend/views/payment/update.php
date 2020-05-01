<?php

/* @var $this yii\web\View */
/* @var $model common\models\Payment */

$this->title = Yii::t('app', 'Update Payment');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Families'),
    'url' => ['family/index']];

$this->params['breadcrumbs'][] = [
    'label' => $family->name,
    'url' => ['family/view', 'id' => $family->id]];

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Financial Details'),
    'url' => ['financial/family-view', 'id' => $family->id]];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="payment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'family' => $family,
    ]) ?>

</div>
