<?php

use common\models\ProgramFamily;
use yii\bootstrap\Html;
use common\helpers\ProgramHelper;
use yii\helpers\Markdown;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $family common\models\Family */
/* @var $programId int */
/* @var $serial integer */
/* @var $familySerial integer */
/* @var $isFirst boolean */
/* @var $rowSpan integer */


echo Html::beginTag('tr', [
    'id' => "program-view-client-$model->id-row",
    'class' => 'program-view-client-row' .
        ($familySerial % 2 === 0 ? ' shaded-row' : ''),
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
        'isFirst' => $isFirst ? 'true' : 'false',
    ],
]);

if ($isFirst) {

    $pf = $family->getProgramFamilies()
        ->where(['program_id' => $programId])->one();

    $statusClass = empty($pf->status) ? '' :
        'status-' . ProgramHelper::getShortStatus()[$pf->status];

    echo Html::tag('td',
        Html::a($familySerial  . ' ' .Html::icon('pencil'), [
                '/program-family/update',
                'family_id' => $family->id,
                'program_id' => $programId,
            ]), [
                'class' =>
                    'program-view-family-serial-cell serial-number-column ' .
                    $statusClass ,
                'id' => "program-view-family-$family->id-serial",
                'data' => [
                    'family-id' => $family->id,
                    'client-id' => $model->id,
            ],
        'rowspan' => $rowSpan,
    ]);

    echo Html::tag('td', Html::a(Html::encode($family->name), [
            '/family/view', 'id' => $family->id,
        ]), [
            'class' => 'program-view-client-wechat-id-cell family-name-column ' .
                $statusClass ,
            'id' => "program-view-client-$model->id-wechat-id",
            'data' => [
                'family-id' => $family->id,
                'client-id' => $model->id,
            ],
            'rowspan' => $rowSpan,
    ]);

    $totalPaid = $family->getPayments()
        ->where(['program_id' => $programId])->sum('amount') ?? 0;

    $pf = ProgramFamily::findOne(
        ['program_id' => $programId, 'family_id' => $family->id]);

    $due = $pf->final_cost ?? 0;

    $balance = $due - $totalPaid;

    echo Html::tag('td',
        Yii::$app->formatter->asCurrency($due),
        [
            'class' => 'program-view-family-status-cell due-cell ' . $statusClass,
            'data' => ['due' => $due],
            'rowspan' => $rowSpan,
        ]
    );

    echo Html::tag('td',
        Yii::$app->formatter->asCurrency($totalPaid),
        [
            'class' => 'program-view-family-status-cell paid-cell ' . $statusClass,
            'data' => ['paid' => $totalPaid],
            'rowspan' => $rowSpan,
        ]
    );

    echo Html::tag('td',
        Yii::$app->formatter->asCurrency($balance),
        [
            'class' => 'program-view-family-status-cell ' .
                'balance-cell ' . $statusClass,
            'id' => "program-view-client-$model->id-balance",
            'data' => [
                'family-id' => $family->id,
                'client-id' => $model->id,
                'balance' => $balance
            ],
            'rowspan' => $rowSpan,
        ]);

    echo Html::tag('td',
        !empty($pf->remarks) ? Markdown::process(Html::encode($pf->remarks)) : '' ,
        [
            'class' => 'program-view-family-status-cell ' .
            'participant-count-column ' . $statusClass,
            'id' => "program-view-client-$model->id-participant-count",
            'data' => [
                'family-id' => $family->id,
                'client-id' => $model->id,
            ],
            'rowspan' => $rowSpan,
        ]);

}

$pc = $model->getProgramClients()->where(['program_id' => $programId])->one();

$statusClass = empty($pc->status) ? '' : 'status-' .
    ProgramHelper::getShortStatus()[$pc->status];

echo Html::tag('td', Html::a($serial, [
        '/program-client/update',
        'program_id' => $programId,
        'client_id' => $model->id,
    ]), [
        'class' => 'program-view-client-serial-cell serial-number-column ' .
            $statusClass ,
        'id' => "program-view-client-$model->id-serial",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
]);

echo Html::tag('td', Html::a(Html::encode($model->getName()),
    ['/client/view', 'id' => $model->id]) .
    ($model->is_kid ? Html::tag('span',
        Yii::t('app', '(kid)'),
        ['class' => 'client-name-display-is-kid']) : ''), [
    'class' => 'program-view-client-name-cell ' . $statusClass,
    'id' => "program-view-client-$model->id-name",
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
    ],
]);

echo Html::tag('td',
    !empty($pc->remarks) ? Markdown::process(Html::encode($pc->remarks)) : '',
    [
    'class' => 'program-view-client-remarks-cell ' . $statusClass,
    'id' => "program-view-client-$model->id-remarks",
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
    ],
]);

echo Html::endTag('tr');