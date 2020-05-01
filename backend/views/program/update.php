<?php

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $pg common\models\ProgramGroup */

$this->title = Yii::t('app', 'Update program {programName}', [
    'programName' => $model->getNamei18n(),
]);
$this->params['breadcrumbs'][] =
    ['label' => Yii::t('app', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] =
    ['label' => $model->getNamei18n(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="program-update">

    <?= $this->render('_form', [
        'model' => $model,
        'pg' => $pg,
    ]) ?>

</div>
