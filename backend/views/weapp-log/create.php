<?php

/* @var $this yii\web\View */
/* @var $model common\models\WeappLog */

$this->title = Yii::t('app', 'Create Weapp Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Weapp Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="weapp-log-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
