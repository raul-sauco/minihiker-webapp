<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
?>
<div class="weapp-info">
    <?php
    if ($model->programGroup->weapp_visible) {
        $message = Yii::t('app',
            'This program is currently displayed on the Weapp');
    } else {
        $message = Yii::t('app',
            'This program is not currently displayed on the Weapp');
    }
    echo Html::tag('span', $message) . '. ';
    echo Html::a(
        Yii::t('app', 'View'),
        ['weapp/view', 'id' => $model->program_group_id])
    ?>
</div>
