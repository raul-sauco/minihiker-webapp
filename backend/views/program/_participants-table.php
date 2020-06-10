<?php
/* @var $this yii\web\View */
/* @var $model common\models\Program */
?>

<table class="program-view-table table table-bordered">
    <thead>
    <tr class="shaded-row">
        <th class="family-serial-column">
            <?= Yii::t('app', 'Family') ?>
        </th>
        <th class="family-name-column">
            <?= Yii::t('app', 'Family Name') ?>
        </th>
        <th class="family-balance-column">
            <?= Yii::t('app', 'Contract Amount') ?>
        </th>
        <th class="family-balance-column">
            <?= Yii::t('app', 'Already Paid') ?>
        </th>
        <th class="family-balance-column">
            <?= Yii::t('app', 'Waiting Payment') ?>
        </th>
        <th class="family-status-column">
            <?= Yii::t('app', 'Remarks') ?>
        </th>
        <th class="serial-column">
            <?= Yii::t('app', 'Client') ?>
        </th>
        <th><?= Yii::t('app', 'Participant') ?></th>
        <th><?= Yii::t('app', 'Remarks') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $serial = 0;
    $familySerial = 0;
    $participantIds = $model->getClients()->select('id')->column();
    foreach ($model->getFamilies()->all() as $family) {
        $clientQuery = $family->getClients()->where(['id' => $participantIds]);
        $clients = $clientQuery->all();
        $participantMemberCount = $clientQuery->count();
        echo $this->render('_familyRow', [
            'clients' => $clients,
            'participantCount' => $participantMemberCount,
            'programId' => $model->id,
            'model' => $family,
            'serial' => $serial,
            'familySerial' => ++$familySerial
        ]);
        $serial += $participantMemberCount;
    }
    ?>
    </tbody>
</table>
