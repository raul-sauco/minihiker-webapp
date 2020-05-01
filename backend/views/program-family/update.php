<?php

/* @var $this yii\web\View */
/* @var $model common\models\ProgramFamily */
/* @var $family common\models\Family */

$this->title = Yii::t('app', 'Update Program Family');

$this->params['breadcrumbs'][] = [
    'label' => $model->program->getNamei18n(),
    'url' => ['program/view', 'id' => $model->program_id]];

$this->params['breadcrumbs'][] = [
    'label' => $model->family->name,
    'url' => ['family/view', 'id' => $model->family_id]];

$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

echo $this->render('/layouts/_createInfo', ['model' => $model]);

?>

<div class="program-family-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?= $this->render('_payments', ['model' => $model]); ?>

</div>
