<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Client */

$nameLink = Html::a(
    Html::encode($model->getName()),
    ['/client/view', 'id' => $model->id]
);
if ($model->familyRole === null) {
    $roleString = Yii::t('app', 'N/A');
} else {
    $roleString = $model->familyRole->getNamei18n();
}

$roleSpan = Html::tag(
    'span',
    '(' . $roleString . ')',
    ['class' => 'client-name-display-is-kid']
);

echo Html::tag(
    'td',
    $nameLink . $roleSpan, [
    'class' => 'program-view-client-name-cell ' . $statusClass,
    'id' => "program-view-client-$model->id-name",
    'data' => [
        'family-id' => $model->family_id,
        'client-id' => $model->id,
    ],
]);
