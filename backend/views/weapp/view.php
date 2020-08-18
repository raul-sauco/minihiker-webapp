<?php

use yii\bootstrap\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */

$model_name = $model->weapp_display_name ?? $model->getNamei18n();
$this->title = Yii::t('app', 'Weapp Data {programName}', [
    'programName' => $model_name,
]);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Programs'),
    'url' => ['program/index']
];
$this->params['breadcrumbs'][] = [
    'label' => $model_name,
    'url' => ['program/view', 'id' => $model->getPrograms()->one()->id]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Weapp Data');
?>

<div id="weapp-program-view">
    <?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>
    <p>
        <?= Html::a(Yii::t('app', 'Update'),
            ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
        ?>
        <?php
        $unanswered = $model->getQas()
            ->where(['answer' => null])
            ->orWhere(['answer' => ''])
            ->count();

        echo Html::a(
            ($unanswered > 0 ? Html::icon('warning-sign') . ' '  : '') .
            Yii::t('app', 'FAQ') .
            ($unanswered > 0 ? " ($unanswered)" : ''),
            ['program-group/qas', 'id' => $model->id], [
                'class' => 'btn ' . ($unanswered > 0 ? 'btn-warning' : 'btn-primary'),
            ]
        ) ?>
    </p>
    <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('app', 'Details'),
                    'content' => $this->render(
                            '_details', ['model' => $model])
                ],
                [
                    'label' => Yii::t('app', 'Preview'),
                    'content' => $this->render(
                            '_phone-preview', ['model' => $model])
                ],
                [
                    'label' => Yii::t('app', 'Images'),
                    'content' => $this->render(
                        '_images', ['model' => $model])
                ]
            ]
    ]) ?>
</div>
