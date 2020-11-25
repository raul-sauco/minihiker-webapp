<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $q string */

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get'
]);
?>
<div class="search-box input-group">
    <span class="input-group-btn">
        <?= Html::a(
                Html::icon('plus') . ' ' .
            Yii::t('app', 'Create User'),
            ['create'], ['class' => 'btn btn-success']
        ) ?>
    </span>
    <input type="text" class="form-control" name="q"
           aria-label="<?= Yii::t('app', 'Search Box')?>"
           placeholder="<?= Yii::t('app', 'Search') . '...' ?>"
           value="<?= $q ?>">
    <span class="input-group-btn">
        <?= Html::submitButton(
            Yii::t('app', 'Search') . ' ' .
            Html::icon('search'),
            ['class' => 'btn btn-primary submit-search-btn']) ?>
    </span>
</div>
<?php ActiveForm::end(); ?>
