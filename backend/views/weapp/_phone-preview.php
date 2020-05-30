<?php

use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */
?>

<div id="phone-container">
    <div id="phone">
        <div id="phone-content">
            <div id="phone-header-image">
                <?= $this->render('_cover-image', ['model' => $model]) ?>
            </div>
            <?= $this->render('_info', ['model' => $model]) ?>
            <div id="pg-phone-tabs">
                <?= Tabs::widget([
                    'items' => [
                        [
                            'label' => Yii::t('app',
                                'Weapp Description'),
                            'content' => $model->weapp_description,
                            'active' => true
                        ],
                        [
                            'label' => Yii::t('app',
                                'Refund Description'),
                            'content' => $model->refund_description
                        ],
                    ]
                ]) ?>
            </div>
        </div>
        <div id="phone-header" class="<?= $model->weapp_visible ?
            'weapp-visible' : 'weapp-invisible' ?>">
            <?= $model->weapp_display_name ?>
        </div>
    </div>
</div>
