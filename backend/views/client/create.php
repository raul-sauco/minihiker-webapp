<?php

/* @var $this yii\web\View */
/* @var $model common\models\Client */

$this->title = Yii::t('app', 'Create Client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
