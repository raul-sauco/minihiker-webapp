<?php

use common\helpers\ClientHelper;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $family common\models\Family */
/* @var $serial integer */
/* @var $isFirst boolean */
/* @var $rowSpan integer */
/* @var $kidCount int */
/* @var $adultCount int */
/* @var $programId int */


echo Html::beginTag('tr', [
    'id' => "program-view-client-$model->id-row",
    'class' => 'program-view-client-row',
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
        'isFirst' => $isFirst ? 'true' : 'false',
    ],
]);

echo Html::tag('td', $serial, [
    'class' => 'program-view-client-serial-cell serial-number-column',
    'id' => "program-view-client-$model->id-serial",
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
    ],
]);

echo Html::tag('td', Html::a(Html::encode($model->getName()),
    ['/client/view', 'id' => $model->id]), [
    'class' => 'program-view-client-name-cell',
    'id' => "program-view-client-$model->id-name",
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
    ],
]);

echo Html::tag('td',
empty($model->id_card_number)? '' : "\u{200C}" . $model->id_card_number, [
    'class' => 'program-view-client-id-card-number-cell',
    'id' => "program-view-client-$model->id-id-card-number",
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
    ],
]);

$expireDate = Yii::t('app', 'Not Set');
if (!empty($model->passport_expire_date)) {
    $expireDate = Yii::$app->formatter->asDate($model->passport_expire_date);
}
echo Html::tag('td', $expireDate, [
    'class' => 'program-view-client-passport-expire-date-cell',
    'id' => "program-view-client-$model->id-passport-expire-date",
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
    ],
]);

$remarks = $model->getProgramClients()
    ->where(['program_id' => $programId])->one()->remarks;

echo Html::tag('td', $remarks ?? '', [
    'class' => 'program-view-client-remarks-cell',
    'id' => "program-view-client-$model->id-remarks",
    'data' => [
        'family-id' => $family->id,
        'client-id' => $model->id,
    ],
]);

if ($isFirst) {

    echo Html::tag('td', ClientHelper::getFamilyWechatId($model), [
        'class' => 'program-view-client-wechat-id-cell wechat-column',
        'id' => "program-view-client-$model->id-wechat-id",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
        'rowspan' => $rowSpan,
    ]);

    echo Html::tag('td',
        Yii::t('app', '{n,plural,=0{no kids} =1{1 kid} other{# kids}}, ' .
            '{i,plural,=0{no adults} =1{1 adult} other{# adults}},', [
                'n' => $kidCount,
                'i' => $adultCount,
        ]), [
        'class' => 'program-view-client-participant-count-cell participant-count-column',
        'id' => "program-view-client-$model->id-participant-count",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
        'rowspan' => $rowSpan,
    ]);

    $phoneNumber = Yii::t('app', 'Not Set');
    if (!empty($family->mother) && !empty($family->mother->phone_number)) {
        $phoneNumber = $family->mother->phone_number;
    } elseif (!empty($family->father) && !empty($family->father->phone_number)) {
        $phoneNumber = $family->father->phone_number;
    }

    echo Html::tag('td', ClientHelper::getFamilyPhoneNumber($model), [
        'class' => 'program-view-client-phone-number-cell',
        'id' => "program-view-client-$model->id-phone-number",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
        'rowspan' => $rowSpan,
    ]);

    $email = Yii::t('app', 'Not Set');
    if (!empty($family->mother) && !empty($family->mother->email)) {
        $email = $family->mother->email;
    } elseif (!empty($family->father) && !empty($family->father->email)) {
        $email = $family->father->email;
    }

    echo Html::tag('td', $email, [
        'class' => 'program-view-client-email-cell',
        'id' => "program-view-client-$model->id-email",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
        'rowspan' => $rowSpan,
    ]);

    $alreadyPaid = $family->getPayments()
        ->where(['program_id' => $programId])->sum('amount');
    echo Html::tag('td',
        Yii::$app->formatter->asCurrency($alreadyPaid ?? 0), [
        'class' => 'program-view-client-paid-cell',
        'id' => "program-view-client-$model->id-paid",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
        'rowspan' => $rowSpan,
    ]);

    $due = $family->getProgramFamilies()
        ->where(['program_id' => $programId])->one()->final_cost;
    echo Html::tag('td',
        Yii::$app->formatter->asCurrency($due ?? 0), [
        'class' => 'program-view-client-due-cell',
        'id' => "program-view-client-$model->id-due",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
        'rowspan' => $rowSpan,
    ]);

    $address = $family->address ?? Yii::t('app', 'Not Set');
    echo Html::tag('td', $address, [
        'class' => 'program-view-client-address-cell',
        'id' => "program-view-client-$model->id-address",
        'data' => [
            'family-id' => $family->id,
            'client-id' => $model->id,
        ],
        'rowspan' => $rowSpan,
    ]);
}

echo Html::endTag('tr');