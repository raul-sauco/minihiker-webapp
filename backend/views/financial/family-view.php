<?php

/* @var $this yii\web\View */
/* @var $family common\models\Family */

$this->title = Yii::t('app', 
    'Financial details for {family} family.', 
    ['family' => $family->name]);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Families'), 
    'url' => ['family/index']];

$this->params['breadcrumbs'][] = [
    'label' => $family->name,
    'url' => ['family/view', 'id' => $family->id],
];

$this->params['breadcrumbs'][] = Yii::t('app', 'Financial Details');

?>

<div class="financial-family-view">

    <?= $this->render('_summary', [
            'family' => $family,
    ]) ?>

    <?= $this->render('_programs', [
        'family' => $family,
    ]) ?>

    <?= $this->render('_expenses', [
            'family' => $family,
    ]) ?>

    <?= $this->render('_payments', [
            'family' => $family,
    ]) ?>

</div>
