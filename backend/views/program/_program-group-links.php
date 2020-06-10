<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $program common\models\Program */

$programs = $model->programGroup->getPrograms()
    ->orderBy('program_period_id')->all();
?>

<div class="program-group-links">

    <?php
    foreach ($programs as $program) {

        $link = $program->programPeriod->name;
        $link .= '. ';
        $link .= Yii::$app->formatter->asDate($program->start_date, 'short');
        $link .= ' - ';
        $link .= Yii::$app->formatter->asDate($program->end_date, 'short');

        $link .= Html::tag('div', $program->getLongParticipantCount());

        echo Html::a(
                $link,
                ['view', 'id' => $program->id],
                [
                    'class' => 'btn btn-sm ' .
                        ($model->id === $program->id ? 'btn-info' : 'btn-default'),
                ]) . ' ';

    }
    ?>

</div>
