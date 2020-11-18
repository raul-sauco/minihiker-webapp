<?php

use common\models\ProgramClient;
use common\models\ProgramFamily;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $pc ProgramClient */

$pf = ProgramFamily::findOne([
    'family_id' => $pc->client->family_id,
    'program_id' => $pc->program_id
]);

$programLink = Html::a("program $pc->program_id",
    ['program/view', 'id' => $pc->program_id]);

$clientLink = Html::a("client $pc->client_id",
    ['client/view', 'id' => $pc->client_id]);

?>
<div class="orphaned-program-family">
    <div><?= "Orphaned $programLink $clientLink" ?></div>
    <div><?= $pf === null ? 'Null related ProgramFamily in program' : 'This result is wrong' ?></div>
    <div><?= $this->render('/layouts/_createInfo', ['model' => $pc]) ?></div>
</div>
