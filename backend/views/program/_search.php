<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use common\models\ProgramType;
use common\models\ProgramGroup;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramSearch */
/* @var $form yii\widgets\ActiveForm */

$pg = new ProgramGroup();
$pg->load(Yii::$app->request->queryParams);
$pt = new ProgramType();
?>

<div id="program-search-box-container" class="index-search-box">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'enableClientScript' => false,
    ]) ?>

    <table class="table table-bordered index-search-table">
        <tbody>
            <tr class="filter-row">

                <td><?= $form->field($pg, 'location_id') ?></td>

                <td><?= $form->field($pg, 'name') ?></td>

                <td><?= $form->field($pg, 'type_id')->dropDownList(
                        ProgramType::find()
                            ->select('name')
                            ->indexBy('id')
                            ->orderBy('id')
                            ->column(),
                        [
                            'prompt' => '',
                        ]) ?>
                </td>

                <td>
                    <?= $form->field($model, 'start_date',
                        ['options' => ['class' => 'form-group']])
                        ->widget(DatePicker::class, [
                            'dateFormat' => 'yyyy-MM-dd',
                            'clientOptions' => [
                                'changeMonth' => true,
                                'changeYear' => true,
                            ],
                            'options' => ['class' => 'form-control'],
                        ])->label(Yii::t('app', 'Starting After'))
                    ?>
                </td>

                <td>
                    <?= $form->field($model, 'end_date', ['options' => ['class' => 'form-group']])
                        ->widget(DatePicker::class, [
                            'dateFormat' => 'yyyy-MM-dd',
                            'clientOptions' => [
                                'changeMonth' => true,
                                'changeYear' => true,
                            ],
                            'options' => ['class' => 'form-control'],
                        ])->label(Yii::t('app', 'Finishing Before'))
                    ?>
                </td>

                <td class="action-links">
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Search'),
                            ['class' => 'btn btn-primary']) ?>
                        <?= Html::resetButton(Yii::t('app', 'Reset'),
                            ['class' => 'btn btn-default']) ?>
                    </div>
                </td>

            </tr>
        </tbody>
    </table>

    <?php ActiveForm::end(); ?>

</div>
