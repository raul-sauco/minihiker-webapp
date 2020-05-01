<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Supplier */
/* @var $contact common\models\Contact */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Create Supplier');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Suppliers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-create">

    <div class="supplier-form">

        <?php $form = ActiveForm::begin([
                'options' => ['id' => 'create-supplier-form']
        ]); ?>

        <div class="row">

            <div class="col-lg-12">

                <header><?= Yii::t('app', 'Supplier\'s Details') ?></header>

            </div>

            <div class="col-lg-6">

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'remarks')->textarea(['rows' => 3]) ?>

            </div>

            <div class="col-lg-6">

                <?= $form->field($model, 'address')->textarea(['rows' => 2]) ?>

                <?= $form->field($model, 'address_2')->textarea(['rows' => 2]) ?>

            </div>

        </div>

        <div class="row">

            <div class="col-lg-12">

                <header><?= Yii::t('app', 'Main Contact\'s Details') ?></header>

            </div>

            <div class="col-lg-4">

                <?= $form->field($contact, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($contact, 'role')->textInput(['maxlength' => true]) ?>

            </div>

            <div class="col-lg-4">

                <?= $form->field($contact, 'phone')->textInput(['maxlength' => true]) ?>

                <?= $form->field($contact, 'wechat_id')->textInput(['maxlength' => true]) ?>

            </div>

            <div class="col-lg-4">

                <?= $form->field($contact, 'email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($contact, 'remarks')->textarea(['rows' => 2]) ?>

            </div>

        </div>

        <div class="row">

            <div class="col-lg-12">

                <div class="form-group">

                    <?= Html::submitButton($model->isNewRecord ?
                        Yii::t('app', 'Create') :
                        Yii::t('app', 'Update'),
                        ['class' => $model->isNewRecord ?
                            'btn btn-success' : 'btn btn-primary']) ?>

                </div>

            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
