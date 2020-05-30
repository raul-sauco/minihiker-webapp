<?php

use common\models\ProgramGroup;
use yii\bootstrap\Tabs;
use yii\web\View;

/* @var $this View */
/* @var $model ProgramGroup */
?>

<div class="weapp-pg-details row">
    <div class="col-lg-3">
        <?= $this->render('_cover-image', ['model' => $model]) ?>
        <div class="weapp-pg-instances-container">
            <?php
            foreach ($model->programs as $program) {
                echo $this->render(
                        '_program-item',
                        ['program' => $program]
                );
            }
            ?>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-3">
                <header>
                    <?= Yii::t('app', 'Display name') ?>
                </header>
                <div class="pg-details">
                    <?= empty($model->weapp_display_name) ?
                        Yii::t('yii', '(not set)') :
                        $model->weapp_display_name
                    ?>
                </div>
                <div class="pg-details">
                    <span class="attr-name">
                        <?= Yii::t('app', 'Weapp Visible') ?>
                    </span>:
                    <span class="attr-value">
                        <?= $model->weapp_visible ?
                            Yii::t('app', 'Yes') :
                            Yii::t('app', 'No')
                        ?>
                </div>
                <div class="pg-details">
                    <span class="attr-name">
                        <?= Yii::t('app', 'Weapp In Banner') ?>
                    </span>:
                    <span class="attr-value">
                        <?= $model->weapp_in_banner ?
                            Yii::t('app', 'Yes') :
                            Yii::t('app', 'No')
                        ?>
                </div>
            </div>
            <div class="col-lg-3">
                <?= $this->render('_info', ['model' => $model]) ?>
            </div>
            <div class="col-lg-3">
                <header><?= Yii::t('app', 'Summary') ?></header>
                <div class="pg-details">
                    <?= $model->summary ?>
                </div>
            </div>
            <div class="col-lg-3">
                <header><?= Yii::t('app', 'Keywords') ?></header>
                <div class="pg-details">
                    <?= $model->keywords ?>
                </div>
            </div>
        </div>
        <div class="row weapp-pg-details-tabs">
            <div class="col-lg-12">
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
    </div>
</div>
