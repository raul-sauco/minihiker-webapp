<?php

/* @var $this yii\web\View */
/* @var $model common\models\ProgramClient */

$this->title = Yii::t('app', 'Update Program Client');

$this->params['breadcrumbs'][] = [
    'label' => $model->program->getNamei18n(),
    'url' => ['program/view', 'id' => $model->program_id]];

$this->params['breadcrumbs'][] = [
    'label' => $model->client->name_zh,
    'url' => ['client/view', 'id' => $model->client_id]];

$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

echo $this->render('/layouts/_createInfo', ['model' => $model])

?>

<div class="program-client-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
