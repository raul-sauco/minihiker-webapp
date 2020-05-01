<?php

/* @var $this yii\web\View */
/* @var $model common\models\Family */

$this->title = Yii::t('app', 'Create Family');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Families'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="family-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
