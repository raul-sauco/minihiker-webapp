<?php

use backend\helpers\WxContentHelper;
use yii\helpers\Json;

/* @var $model \common\models\ProgramGroup */
/* @var $this \yii\web\View */

$result = WxContentHelper::copyImagesToLocalServer($model);

?>

<div class="row">
    <div class="col-lg-6">
        <?= $model->weapp_description ?>
    </div>
    <div class="col-lg-6">
        <?= $result ?>
    </div>
</div>
