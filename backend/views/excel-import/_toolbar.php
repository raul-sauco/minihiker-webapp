<?php
use yii\bootstrap\Html;
?>

<div class="btn-group excel-import-actions"
     :class="{ blur: modal.visible }">
    <?= Html::button(
        Html::icon('upload') . ' ' .
        Yii::t('app', 'Upload all'), [
            'class' => 'btn btn-success',
            '@click' => 'uploadAllRows'
        ]
    ) ?>
    <?= Html::button(
        Html::icon('question-sign'), [
            'class' => 'btn btn-primary',
            '@click' => 'showHelpModal',
            'title' => Yii::t('app', 'Help page')
        ]
    ) ?>
</div>
