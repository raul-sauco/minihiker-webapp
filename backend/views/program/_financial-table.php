<?php

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$js = 'calculateOverallFinancial();';
$this->registerJs($js);
?>

<table class="program-view-table table table-bordered table-striped">
    <thead><tr>
        <th><?= Yii::t('app', 'Participant Count') ?></th>
        <th><?= Yii::t('app', 'Total Contract Balance') ?></th>
        <th><?= Yii::t('app', 'Total Amount Paid') ?></th>
        <th><?= Yii::t('app', 'Total Waiting Payment') ?></th>
    </tr></thead>
    <tbody><tr>
        <td id="program-view-overview-participants">
            <?= $model->getLongParticipantCount() ?>
        </td>
        <td id="program-view-overview-due"></td>
        <td id="program-view-overview-paid"></td>
        <td id="program-view-overview-balance"></td>
    </tr></tbody>
</table>
