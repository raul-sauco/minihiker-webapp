<?php

/* @var $this yii\web\View */
/* @var $model common\models\Expense */

$this->title = Yii::t('app', 'Create Expense');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
