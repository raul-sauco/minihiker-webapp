<?php

use common\models\Wallet;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $paymentDataProvider yii\data\ActiveDataProvider */
/* @var $expenseDataProvider yii\data\ActiveDataProvider */
/* @var $family common\models\Family */
?>

<div class="summary">
    <table class="table table-stripped table-bordered">

        <thead>
        <tr>
            <th><?= Yii::t('app', 'Program Name')?></th>
            <th><?= Yii::t('app', 'Payments made')?></th>
            <th><?= Yii::t('app', 'Total amount due')?></th>
            <th><?= Yii::t('app', 'Balance')?></th>
        </tr>
        </thead>

        <tbody>

        <tr id="program-row">

            <td>
                <?= Yii::t('app', 'Program Overview') ?>
            </td>

            <td id="financial-family-index-paid-programs">
                <?= Yii::$app->formatter->asCurrency(
                    $paid = $family->getPayments()
                        ->where(['IS NOT', 'program_id', null])
                        ->sum('amount')) ?>
            </td>

            <td id="financial-family-index-due-programs">
                <?= Yii::$app->formatter->asCurrency(
                    $due = $family->getProgramFamilies()->sum('final_cost')) ?>
            </td>

            <?php $balance = $paid - $due; ?>

            <?= Html::tag(
                'td' ,
                Yii::$app->formatter->asCurrency($balance) ,
                [
                    'class' => ($balance < 0) ? 'financial-negative-balance' : '',
                    'id' => 'financial-family-index-balance-programs',
                ])
            ?>

        </tr>

        <tr id="Outdoor-card-row">

            <td>
                <?= Yii::t('app', 'Outdoor class stored value card') ?>
            </td>

            <td id="financial-family-index-paid-card">
                <?= Yii::$app->formatter->asCurrency(
                    $paid = $family->getPaid(Wallet::WALLET_TYPE_CARD)) ?>
            </td>

            <td id="financial-family-index-due-card">
                <?= Yii::$app->formatter->asCurrency(
                    $due = $family->getDue(Wallet::WALLET_TYPE_CARD)) ?>
            </td>

            <?php $balance = $paid - $due; ?>

            <?= Html::tag(
                'td' ,
                Yii::$app->formatter->asCurrency($balance) ,
                [
                    'class' => ($balance < 0) ? 'financial-negative-balance' : '',
                    'id' => 'financial-family-index-balance-card',
                ])
            ?>

        </tr>

        <tr id="Reserve-row">

            <td>
                <?= Yii::t('app', 'Set aside value') ?>
            </td>

            <td id="financial-family-index-paid-reserve">
                <?php
                $paid = $family->getPaid(Wallet::WALLET_TYPE_RESERVE) * 1;
                echo Yii::$app->formatter->asCurrency($paid);
                ?>
            </td>

            <td id="financial-family-index-due-reserve">
                <?= Yii::$app->formatter->asCurrency(
                    $due = $family->getDue(Wallet::WALLET_TYPE_RESERVE)) ?>
            </td>

            <?php $balance = $paid - $due; ?>

            <?= Html::tag(
                'td' ,
                Yii::$app->formatter->asCurrency($balance) ,
                [
                    'class' => ($balance < 0) ? 'financial-negative-balance' : '',
                    'id' => 'financial-family-index-balance-reserve',
                ])
            ?>

        </tr>

        </tbody>

    </table>
</div>

