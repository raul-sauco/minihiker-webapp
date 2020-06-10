<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
?>

<p>
    <?= Html::a(Yii::t('app', 'Add Period'),
        ['create', 'group_id' => $model->program_group_id],
        ['class' => 'btn btn-success']) ?>

    <?= Html::a(Yii::t('app', 'Update'),
        ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    <?php
    $unanswered = $model->programGroup->getQas()
        ->where(['answer' => null])
        ->orWhere(['answer' => ''])
        ->count();

    echo Html::a(
        ($unanswered > 0 ? Html::icon('warning-sign') . ' '  : '') .
        Yii::t('app', 'FAQ') .
        ($unanswered > 0 ? " ($unanswered)" : ''),
        ['program-group/qas', 'id' => $model->program_group_id], [
            'class' => 'btn ' . ($unanswered > 0 ? 'btn-warning' : 'btn-primary'),
        ]
    ) ?>

    <?= Html::a(
        Yii::t('app', 'Update Participants'),
        ['program-client/update-program-clients', 'program_id' => $model->id],
        ['class' => 'btn btn-primary']
    ) ?>

    <?= Html::a(
        Yii::t('app', 'Update Guides'),
        ['update-guides', 'id' => $model->id],
        ['class' => 'btn btn-primary']
    ) ?>

    <?= Html::a(
        Yii::t('app', 'Export'),
        ['export', 'id' => $model->id],
        ['class' => 'btn btn-primary']
    ) ?>

    <?= Html::a(
        Yii::t('app', 'Delete'),
        ['delete', 'id' => $model->id],
        [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app',
                    'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
</p>

