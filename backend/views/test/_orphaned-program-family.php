<?php

use common\models\ProgramClient;
use common\models\ProgramFamily;

/* @var $this yii\web\View */
/* @var $pf ProgramFamily */

$clientIds = $pf->family->getClients()->select('id')->all();
$pcQuery = ProgramClient::find()->where([
    'program_id' => $pf->program_id,
    'client_id' => $clientIds
]);

?>
<div class="orphaned-program-family">
    <div><?= "Orphaned Program $pf->program_id Family $pf->family_id" ?></div>
    <div><?= $pcQuery->count() . ' clients from family in program' ?></div>
    <div><?= $this->render('/layouts/_createInfo', ['model' => $pf]) ?></div>
</div>
