<?php

/* @var $this yii\web\View */

use common\helpers\ProgramClientHelper;
use common\models\ProgramClient;
use yii\bootstrap\Html;

?>

<div class="tests-program-client-integrity">
    <?php
    $query = ProgramClientHelper::getOrphanedProgramClients();
    $count = $query->count();
    if ($count > 0) {
        echo Html::tag('h4',
            "$count Orphaned Program Clients");
        /** @var ProgramClient $opc */
        foreach ($query->each() as $opc) {
            echo $this->render('_orphaned-program-client', ['pc' => $opc]);
        }
    } else {
        echo Html::tag('h4','No orphaned Program Client records');
    }
    ?>
</div>
