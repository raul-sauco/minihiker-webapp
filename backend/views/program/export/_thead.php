<?php
use yii\bootstrap\Html;
/* @var $this yii\web\View */
$headers = [
    [
        'name' => Yii::t('app', 'Serial Number'),
        'css' => 'serial'
    ],
    [
        'name' => Yii::t('app', 'Participant Name'),
        'css' => 'name'
    ],
    [
        'name' => Yii::t('app', 'Id Card Number'),
        'css' => 'id-card-number'
    ],
    [
        'name' => Yii::t('app', 'Passport Expiry Date'),
        'css' => 'passport-expire-date'
    ],
    [
        'name' => Yii::t('app', 'Remarks'),
        'css' => 'remarks'
    ],
    [
        'name' => Yii::t('app', 'Wechat Username'),
        'css' => 'wechat-id'
    ],
    [
        'name' => Yii::t('app', 'Participant Count'),
        'css' => 'participant-count'
    ],
    [
        'name' => Yii::t('app', 'Phone Number'),
        'css' => 'phone-number'
    ],
    [
        'name' => Yii::t('app', 'Email'),
        'css' => 'email'
    ],
    [
        'name' => Yii::t('app', 'Already Paid'),
        'css' => 'paid'
    ],
    [
        'name' => Yii::t('app', 'Program Fee'),
        'css' => 'due'
    ],
    [
        'name' => Yii::t('app', 'Address'),
        'css' => 'address'
    ],
];
?>
<thead>
<tr>
    <?php foreach($headers as $idx => $header) : ?>
    <th class="program-view-client-<?= $header['css'] ?>-cell">
        <div class="column-header-wrapper">
            <div><?= $header['name'] ?></div>
            <?= Html::checkbox('exp-date', true, [
                'class' => 'toggle-column-checkbox',
                'data-column-index' => $idx,
                'data-attr' => $header['css']
            ]) ?>
        </div>
    </th>
    <?php endforeach; ?>
</tr>
</thead>
