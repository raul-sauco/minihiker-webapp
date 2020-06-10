<?php

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = $model->getNamei18n();
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Programs'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-view">
	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>
    <?= $this->render('_view-action-links', ['model' => $model]) ?>
    <?= $this->render('_program-group-links', ['model' => $model]) ?>
    <?= $this->render('_remarks', ['model' => $model]) ?>
    <?= $this->render('_financial-table', ['model' => $model]) ?>
    <?= $this->render('_participants-table', ['model' => $model]) ?>
    <?= $this->render('_guide-grid', ['model' => $model]) ?>
    <?= $this->render('_weapp-info', ['model' => $model]) ?>
</div>
