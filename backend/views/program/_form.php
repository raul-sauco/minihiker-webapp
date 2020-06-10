<?php

use common\models\ProgramPeriod;
use common\models\ProgramType;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $pg common\models\ProgramGroup */
/* @var $form yii\widgets\ActiveForm */
?>
    
<?php $form = ActiveForm::begin(['id' => 'program-form']); ?>

<div class="program-form" id="program-create-update-form" data-program-id="<?= $model->id ?>">

    <div class="row">

        <div class="col-lg-4">

            <?= $form->field($pg, 'name')->textInput() ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($pg, 'type_id')->dropDownList(
                ProgramType::find()
                    ->select('name')
                    ->indexBy('id')
                    ->orderBy('id')
                    ->column(),
                [
                    'prompt' => Yii::t('app', 'Select Type'),
                ]) ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($pg, 'location_id')->widget(Select2::class, [
                'options' => ['placeholder' => Yii::t('app', 'Select Location')],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => Url::to(['location/list']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(location) { return location.text; }'),
                    'templateSelection' => new JsExpression('function (location) { return location.text; }'),
                ],

            ]) ?>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-4">

            <?= $form->field($model, 'program_period_id')->dropDownList(
                ProgramPeriod::find()
                    ->orderBy('id')
                    ->select('name')
                    ->indexBy('id')
                    ->column()
            )
            ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($model, 'start_date', ['options' => ['class' => 'form-group']])
                ->widget(DatePicker::class, [
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                    'options' => ['class' => 'form-control'],
                ])
            ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($model, 'end_date', ['options' => ['class' => 'form-group']])
                ->widget(DatePicker::class, [
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                    'options' => ['class' => 'form-control'],
                ])
            ?>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-4">

            <?= $form->field($model, 'registration_open')->dropDownList([
                0 => Yii::t('app', 'No'),
                1 => Yii::t('app', 'Yes')
            ]) ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($model, 'client_limit')->textInput() ?>

        </div>

        <div class="col-lg-4">

            <?= $form->field($pg, 'accompanied')->dropDownList([
                '0' => Yii::t('app', 'Only children'),
                '1' => Yii::t('app', 'With parents')
            ])->label(Yii::t('app', 'Program Type')) ?>

        </div>

    </div>

    <div class="row">

        <?php if (!$model->isNewRecord) : ?>

        <div class="col-lg-6">

            <header id="program-prices-header">
                <?= Html::tag('div', Yii::t('app', 'Price')) ?>
                <?= Html::button(
                        '添加',[
                            'class' => 'btn btn-xs btn-success',
                            'onclick' => "showProgramPriceCreateForm($model->id)"
                        ])
                ?>
            </header>

            <?= Html::tag('div', '', [
                    'id' => 'program-prices-container',
                    'data' => [
                        'create-url' => Url::to(['program-price/create']),

                        // Site.js expects the update url to have a '0' as parametrized id
                        'update-url' => Url::to(['program-price/update', 'id' => 0])
                    ]
            ]) ?>

        </div>

        <?php endif; ?>

        <div class="col-lg-<?= $model->isNewRecord ? '12' : '6' ?>">

            <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

            <?= $form->field($pg, 'keywords')->textarea(['rows' => 6]) ?>

        </div>

    </div>

    <div class="row">
        <div class="form-group col-lg-4">

            <?= Html::submitButton($model->isNewRecord ?
                Yii::t('app', 'Create') :
                Yii::t('app', 'Update'),
                ['class' => $model->isNewRecord ?
                    'btn btn-success' : 'btn btn-primary']) ?>

        </div>
    </div>

</div>

<?php
ActiveForm::end();
if (!$model->isNewRecord) {
    $this->registerJs("fetchProgramPrices($model->id)",
        View::POS_READY,
        'fetchProgramPricesJs'
    );
}
