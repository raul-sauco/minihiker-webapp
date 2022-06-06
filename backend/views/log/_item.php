<?php

use common\helpers\LogHelper;
use common\models\Log;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model Log */


?>
<div class="log-item-container">
    <div class="log-item-header">
        <div class="log-item-id">
            <?= Html::a(
                "#$model->id",
                ['view', 'id' => $model->id],
            ) ?>
        </div>
        <div class="log-item-level"><?= LogHelper::getLevelText($model) ?></div>
        <div class="log-item-category"><?= $model->category ?></div>
        <div class="log-item-log-time">
            <?= Yii::$app->formatter->asDateTime($model->log_time) ?>
        </div>
    </div>
    <div class="log-item-prefix"><?= $model->prefix ?></div>
    <div class="log-item-message">
        <?= Yii::$app->formatter->asNtext($model->message) ?>
    </div>
</div>
