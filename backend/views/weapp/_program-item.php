<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $program common\models\Program */
?>
<a href="<?= Url::to([
    'program/update',
    'id' => $program->id,
    'ref' => 'weapp'])
?>">
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
            <?= Yii::t('app',
                '{current} of {max} registrations.', [
                'current' => $program->getClients()->count(),
                'max' => empty($program->client_limit) ? 0 :
                    $program->client_limit
            ]) ?>
        </div>
        <div class="weapp-program-instance-prices">
            <?php
            $prices = $program->programPrices;
            if (count($prices) > 0) {
                echo Html::tag('header',
                    Yii::t('app', 'Price'));
                echo Html::beginTag('ol');
                foreach ($program->programPrices as $price) {
                    echo Html::tag('li',
                        Yii::t('app',
                            '{adults} adults, {kids} kids, {membership_type} {price}', [
                                'adults' => $price->adults,
                                'kids' => $price->kids,
                                'membership_type' => $price->membership_type === 1 ?
                                    Yii::t('app', 'Member') :
                                    Yii::t('app', 'Not Member'),
                                'price' => $price->price
                            ]), ['class' => 'program-price']);
                }
                echo Html::endTag('ol');
            } else {
                // The program does not have any prices
                echo Html::tag('header',
                    Yii::t('app',
                        'You have not added any prices to the program yet.'
                    ));
            }
            ?>
        </div>
    </div>
</a>
