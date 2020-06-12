<?php

use vova07\imperavi\Widget;
use yii\bootstrap\Tabs;
use yii\helpers\Url;

/* @var $this yii\web\View */

$config = [
    'settings' => [
        'lang' => Yii::$app->language === 'zh-CN' ? 'zh_cn' : 'en',
        'minHeight' => 200,
        'maxHeight' => 800,
        'imageUpload' => Url::to(['/imperavi/image-upload']),
        'imageManagerJson' => Url::to(['/imperavi/images-get']),
        'imageDelete' => Url::to(['/imperavi/file-delete']),
        'plugins' => [
            'fontsize',
            'clips',
            'fullscreen',
            'imagemanager'
        ],
        'clips' => [
            ['Lorem ipsum...', 'Lorem...'],
            ['red', '<span class="label-red">red</span>'],
            ['green', '<span class="label-green">green</span>'],
            ['blue', '<span class="label-blue">blue</span>'],
        ],
    ],
];

echo Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('app', 'Weapp Description'),
            'content' => $form->field($model, 'weapp_description')
                ->widget(Widget::class, $config),
            'active' => true
        ],
        [
            'label' => Yii::t('app', 'Refund Description'),
            'content' => $form->field($model, 'refund_description')
                ->widget(Widget::class, $config)
        ],
    ]
]);
