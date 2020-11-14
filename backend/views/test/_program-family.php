<?php

/* @var $this yii\web\View */

use common\helpers\ProgramClientHelper;
use common\helpers\ProgramFamilyHelper;
use common\models\ProgramFamily;
use yii\bootstrap\Html;
?>

<div class="site-permissions-tests">
    <h3>ProgramClient / ProgramFamily integrity.</h3>
    <h4>Orphaned ProgramFamilies</h4>
    <?php
    /** @var ProgramFamily $opf */
    foreach (ProgramFamilyHelper::getOrphanedProgramFamilies() as $opf) {
        echo Html::tag('div',
            "Orphaned Program $opf->program_id Family $opf->family_id");
    }
    ?>
    <h4>Orphaned ProgramClients</h4>
    <?php
    /** @var ProgramClient $oph */
    foreach (ProgramClientHelper::getOrphanedProgramClients() as $opc) {
        echo Html::tag('div',
            "Orphaned Program $opc->program_id Family $opc->family_id");
    }
    ?>
</div>
