<?php

/* @var $this yii\web\View */
/* @var $model common\models\Family */

use common\helpers\FamilyHelper;
use yii\bootstrap\Html; ?>

<div class="family-search-item material-card">

    <div class="family-search-item-details">

        <?= Html::tag('div',
            $model->name,
            ['class' => 'family-search-item-name'
            ])
        ?>

        <?= Html::tag('div',
            $model->category,
            ['class' => 'family-search-item-category'
            ])
        ?>

        <?= Html::tag('div',
            FamilyHelper::getFormattedSerial($model),
            ['class' => 'family-search-item-serial-number'
            ])
        ?>

    </div>

    <?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <?php
    if (!empty($model->membership_date)) {
        echo Html::tag('div',
            Yii::t('app', 'Membership Date') . ': ' .
            Yii::$app->formatter->asDate($model->membership_date),
            ['class' => 'family-search-item-membership-date']);
    }

    if (!empty($model->address)) {
        echo Html::tag('div',
            Yii::t('app', 'Address') . ': ' .
            $model->address,
            ['class' => 'family-search-item-address']);
    }

    if (!empty($model->place_of_residence)) {
        echo Html::tag('div',
            Yii::t('app', 'Place Of Residence') . ': ' .
            $model->place_of_residence,
            ['class' => 'family-search-item-address']);
    }

    if (!empty($model->remarks)) {
        echo Html::tag('div',
            Yii::t('app', 'Remarks') . ': ' .
            $model->place_of_residence,
            ['class' => 'family-search-item-remarks']);
    }
    ?>

    <header>
        <?= Yii::t('app', 'Family members') ?>
    </header>

    <div class="family-search-item-members">
        <?php
        foreach ($model->clients as $client) {

            echo Html::tag('div',
                $client->getName() . (empty($client->familyRole) ? '' : ' ('  .
                $client->familyRole->getNamei18n() . ')'),
                ['class' => 'family-search-item-member']);

        }
        ?>
    </div>

    <div class="family-search-item-actions">

        <?= Html::a(
                Yii::t('app', 'Select'),
                [
                    'family/merge-confirm',
                    'id' => Yii::$app->request->get('id'),
                    'dup' => $model->id
                ],
                ['class' => 'btn btn-primary']
        ) ?>

    </div>

</div>
