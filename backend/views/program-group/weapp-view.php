<?php

use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

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

<div class="program-group-weapp-data">

    <?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <p>

        <?= Html::a(Yii::t('app', 'Update'),
            ['weapp-update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    </p>

    <div id="phone-container">

        <div id="phone">

            <div id="phone-content">

                <div id="phone-header-image">
                    <?= Html::img(
                        '@web/img/pg/' . $model->id . '/' . $model->weapp_cover_image,
                        ['alt' => 'Cover image'])
                    ?>
                </div>

                <div class="pg-details">

                    <?= Yii::t('app', 'Theme') .
                    ': ' . $model->theme ?>

                </div>

                <div class="pg-details">

                    <?= Yii::t('app', 'Location') .
                    ': ' . $model->location_id ?>

                </div>

                <div class="pg-details">

                    <?= Yii::t('app', 'Periods') .
                    ': ' . $model->getPrograms()->count() ?>

                </div>

                <div class="pg-details">

                    <?= Yii::t('app', 'Age') .
                    ': ' . $model->min_age . ' - ' . $model->max_age ?>

                </div>

                <div class="pg-details">

                    <?= Yii::t('app', 'Accompanied') .
                    ': ' . ($model->accompanied ? Yii::t('app', 'Accompanied') :
                        Yii::t('app', 'Only childs')) ?>

                </div>

                <div id="pg-phone-tabs">
                    <?= Tabs::widget([
                        'items' => [
                            [
                                'label' => Yii::t('app', 'Weapp Description'),
                                'content' => $model->weapp_description,
                                'active' => true
                            ],
                            [
                                'label' => Yii::t('app', 'Refund Description'),
                                'content' => $model->refund_description
                            ],
                        ]
                    ]) ?>
                </div>

            </div>

            <div id="phone-header" class="<?= $model->weapp_visible ? 'weapp-visible' : 'weapp-invisible' ?>">
                <?= $model->weapp_display_name ?>
            </div>

        </div>

    </div>

    <div class="weapp-pg-instances-container">

        <?php foreach ($model->programs as $program) : ?>

        <a href="<?= Url::to(['program/update', 'id' => $program->id, 'ref' => 'weapp']) ?>">

            <div class="weapp-pg-instance-container">

                <h3 class="weapp-program-instance-name">
                    <?= $program->getNamei18n() ?>
                </h3>

                <div class="weapp-program-instance-dates">
                    <?= Yii::t('app', '{start_date} to {end_date}', [
                            'start_date' => Yii::$app->formatter->asDate($program->start_date),
                            'end_date' => Yii::$app->formatter->asDate($program->end_date)
                    ]) ?>
                </div>

                <div class="weapp-program-instance-registration">

                    <div class="weapp-program-instance-registration-status">
                        <?= $program->registration_open ?
                            Yii::t('app', 'Registration Open') :
                            Yii::t('app', 'Registration Closed')
                        ?>
                    </div>

                </div>

                <div class="weapp-program-instance-registration-numbers">
                    <?= Yii::t('app', '{current} of {max} registrations.', [
                        'current' => $program->getClients()->count(),
                        'max' => empty($program->client_limit) ? 0 : $program->client_limit
                    ]) ?>
                </div>

                <div class="weapp-program-instance-prices">
                    <?php
                    $prices = $program->programPrices;

                    if (count($prices) > 0) {

                        echo Html::tag('header', Yii::t('app', 'Price'));

                        echo Html::beginTag('ol');

                        foreach ($program->programPrices as $price) {

                            echo Html::tag('li',
                                Yii::t('app',
                                    '{adults} adults, {kids} kids, {membership_type} {price}', [
                                        'adults' => $price->adults,
                                        'kids' => $price->kids,
                                        'membership_type' => $price->membership_type === 1 ? Yii::t('app', 'Member') : Yii::t('app', 'Not Member'),
                                        'price' => $price->price
                                    ]), ['class' => 'program-price']);

                        }

                        echo Html::endTag('ol');

                    } else {

                        // The program does not have any prices
                        echo Html::tag('header',
                            Yii::t('app', 'You have not added any prices to the program yet.'));
                    }
                    ?>
                </div>

            </div>

        </a>

        <?php endforeach; ?>

    </div>

    <?php

    foreach ($model->images as $image) {

        $imageTag = Html::img(
                "@web/img/pg/$model->id/th/" . $image->name, [
                'alt' => $image->name,
                'class' => 'img-thumbnail'
        ]);

        echo $imageTag;

    }
    ?>
</div>
