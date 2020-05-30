<?php

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */
?>
<div class="pg-details">
    <span class="attr-name">
        <?= Yii::t('app', 'Theme') ?>
    </span>:
    <span class="attr-value">
        <?= empty($model->theme) ?
            Yii::t('yii', '(not set)') : $model->theme
        ?>
    </span>
</div>
<div class="pg-details">
    <span class="attr-name">
        <?= Yii::t('app', 'Location') ?>
    </span>:
    <span class="attr-value">
        <?= empty($model->location_id) ?
            Yii::t('yii', '(not set)') : $model->location_id
        ?>
    </span>
</div>
<div class="pg-details">
    <span class="attr-name">
        <?= Yii::t('app', 'Periods') ?>
    </span>:
    <span class="attr-value">
        <?= $model->getPrograms()->count() ?>
    </span>
</div>
<div class="pg-details">
    <span class="attr-name">
        <?= Yii::t('app', 'Age') ?>
    </span>:
    <span class="attr-value">
        <?= $model->min_age . ' - ' . $model->max_age ?>
    </span>
</div>
<div class="pg-details">
    <span class="attr-name">
        <?= Yii::t('app', 'Accompanied') ?>
    </span>:
    <span class="attr-value">
        <?= $model->accompanied ?
            Yii::t('app', 'Accompanied') :
            Yii::t('app', 'Only children') ?>
    </span>
</div>
