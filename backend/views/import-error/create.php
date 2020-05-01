<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ImportError */

$this->title = Yii::t('app', 'Create Import Error');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Import Errors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-error-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
