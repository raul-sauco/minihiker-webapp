<?php

/* @var $this yii\web\View */
/* @var $model common\models\Family */
/* @var $serial integer */
/* @var $familySerial integer */
/* @var $programId int */
/* @var $clients common\models\Client[] */
/* @var $participantCount integer */

$rowSpan = $participantCount;
$isFirst = true;

foreach ($clients as $client) {

    $serial ++;

    echo $this->render('_clientRow', [
        'programId' => $programId,
        'model' => $client,
        'family' => $model,
        'serial' => $serial,
        'familySerial' => $familySerial,
        'isFirst' => $isFirst,
        'rowSpan' => $rowSpan
    ]);

    // Set the switch to false
    $isFirst = false;

}

return $serial;
