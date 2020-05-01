<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Contact */

$this->title = Yii::t('app', 'Add Contact');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Suppliers'),
    'url' => ['supplier/index']
];

$this->params['breadcrumbs'][] = [
    'label' => $model->supplier->name,
    'url' => ['supplier/view', 'id' => $model->supplier_id]
];

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contact-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
