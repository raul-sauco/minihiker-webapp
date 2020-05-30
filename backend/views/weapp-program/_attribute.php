<?php

/* @var $model common\models\ProgramGroup */
/* @var $attr string */
/* @var $this yii\web\View */

$attrClass = 'pg-attr-empty-value';
$value = Yii::t('app', 'Not Set');
$label = $model->getAttributeLabel($attr);

if (!empty($model->getAttribute($attr))) {
    $attrClass = 'pg-attr-value';
    $value = $model->getAttribute($attr);
}

if ($attr === 'accompanied') {
    $attrClass = 'pg-attr-value';
    $value = $model->getAttribute($attr) ? Yii::t('app', 'With parents') :
        Yii::t('app', 'Only childs');
}

if ($attr === 'age') {
    $attrClass = 'pg-attr-value';
    $label = Yii::t('app', 'Age');
    $value = $model->min_age . ' - ' . $model->max_age;
}

if ($attr === 'period') {
    $attrClass = 'pg-attr-value';
    $label = Yii::t('app', 'Type');
    $value = $model->type->name;
}

if ($attr === 'registration') {
    $attrClass = 'pg-attr-value';
    $label = Yii::t('app', 'Status');
    $value = Yii::t('app', 'Registration Closed');

    foreach ($model->programs as $program) {

        if ($program->registration_open) {
            $value = Yii::t('app', 'Registration Open');
        }
    }
}

?>

<div class="pg-attribute-container pg-<?= $attr ?>">

    <span class="pg-attr-name">
        <?= $label ?>:
    </span>

    <span class="<?= $attrClass ?>">
        <?= $value ?>
    </span>

</div>
