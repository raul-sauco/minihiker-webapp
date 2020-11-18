<?php

/* @var $this yii\web\View */

use common\helpers\ProgramFamilyHelper;
use common\models\ProgramFamily;
use yii\bootstrap\Html;

?>

<div class="site-permissions-tests">
    <?php
    $pfQuery = ProgramFamilyHelper::getOrphanedProgramFamilies();
    $count = $pfQuery->count();
    if ($count > 0) {
        $button = Html::a('Fix it',
            ['test/fix-orphaned-program-families'],
            ['class' => 'btn btn-small btn-primary']
        );
        echo Html::tag('h4',
            "$count Orphaned Program Families " . $button);
        /** @var ProgramFamily $opf */
        foreach ($pfQuery->each() as $opf) {
            echo $this->render('_orphaned-program-family', ['pf' => $opf]);
        }
    } else {
        echo Html::tag('h4','No orphaned Program Family records');
    }
    ?>
</div>
