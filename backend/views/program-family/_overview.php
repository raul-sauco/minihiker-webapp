<?php

/* @var $this yii\web\View */
/* @var $model common\models\ProgramFamily */

$totalPayments = $model->family->getPayments()
    ->where(['program_id' => $model->program_id])
    ->sum('amount');

$balance = $totalPayments - $model->final_cost;

?>

<table class="table table-bordered">

    <thead>

        <tr>

            <th><?= Yii::t('app', 'Total Cost') ?></th>

            <th><?= Yii::t('app', 'Total Paid') ?></th>

            <th><?= Yii::t('app', 'Balance') ?></th>

        </tr>

    </thead>

    <tbody>

        <tr>

            <td id="program-family-overview-final-cost">
                <?= Yii::$app->formatter->asCurrency($model->final_cost) ?>
            </td>

            <td><?= Yii::$app->formatter->asCurrency($totalPayments) ?></td>

            <td id="program-family-overview-balance"
                class="<?= ($balance < 0) ? 'financial-negative-balance' : '' ?>"
                data-total-paid="<?= $totalPayments ?>">
                <?= Yii::$app->formatter->asCurrency($balance) ?>
            </td>

        </tr>

    </tbody>

</table>
