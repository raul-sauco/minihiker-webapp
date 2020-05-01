<?php

use yii\bootstrap\Html;

/* @var $model common\models\Supplier */
/* @var $this yii\web\View */

foreach ($model->contacts as $contact) {

    echo Html::beginTag('p', []);

    echo Html::a($contact->name ?? Yii::t('app', 'No Name'),
        ['contact/view', 'id' => $contact->id],[
        'class' => 'supplier-index-contact-name'
    ]);

    echo Html::tag('span', $contact->role ?? '', [
        'class' => 'supplier-index-contact-role'
    ]);

    echo Html::endTag('p');

    echo Html::beginTag('p', []);

    if ($contact->phone !== null) {

        $phone = Html::tag('span',
            Html::icon('earphone') . $contact->phone, [
            'class' => 'supplier-index-contact-phone'
        ]);

        $phoneUrl = 'tel:' . preg_replace(
            '~\s~',
            '',
            trim($contact->phone));

        echo Html::a($phone,
        $phoneUrl);
    }

    if ($contact->wechat_id !== null) {

        echo Html::tag(
            'span',
            Html::icon('wechat') .
            Html::encode($contact->wechat_id), [
            'class' => 'supplier-index-contact-wechat-id'
        ]);

    }

    echo Html::endTag('p');

}