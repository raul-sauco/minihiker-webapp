<?php

/* @var $this yii\web\View */
/* @var $model common\models\Family */
/* @var $serial integer */
/* @var $clients common\models\Client[] */
/* @var $participantCount integer */
/* @var $kidCount int */
/* @var $adultCount int */
/* @var $programId int */

$rowSpan = $participantCount;
$isFirst = true;

foreach ($clients as $client) {

    $serial ++;

    echo $this->render('_clientRow', [
        'programId' => $programId,
        'model' => $client,
        'family' => $model,
        'serial' => $serial,
        'isFirst' => $isFirst,
        'rowSpan' => $rowSpan,
        'kidCount' => $kidCount,
        'adultCount' => $adultCount,
    ]);

    // Set the switch to false
    $isFirst = false;

}

return $serial;
?>
