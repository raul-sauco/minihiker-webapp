<?php

/* @var $this yii\web\View */
/* @var $model common\models\Client */

$this->title = Yii::t('app', 'Update Client: ') . $model->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_zh, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="client-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
