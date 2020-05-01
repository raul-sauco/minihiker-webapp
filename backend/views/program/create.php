<?php

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $pg common\models\ProgramGroup */

$this->title = Yii::t('app', 'Create Program');

$this->params['breadcrumbs'][] =
    ['label' => Yii::t('app', 'Programs'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="program-create">

    <?= $this->render('_form', [
        'model' => $model,
        'pg' => $pg,
    ]) ?>

</div>
