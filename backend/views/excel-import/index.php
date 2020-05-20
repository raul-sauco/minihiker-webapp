<?php

use common\assets\VueAsset;
use common\assets\XlsxAsset;use yii\bootstrap\Html;

/* @var $this \yii\web\View */

$this->registerJsFile('@staticUrl/js/excel-import.js', [
    'depends' => [
        VueAsset::class,
        XlsxAsset::class
    ]
]);

?>
<div id="excel-import-container">
    <div class="table-load-results" v-if="sheet !== null">
        <?= $this->render('_toolbar') ?>
        <?= $this->render('_table-display') ?>
        <?= $this->render('_modal') ?>
    </div>
    <div class="form-container" v-else>
        <form>
            <h1><?= Yii::t('app', 'Spreadsheet upload') ?></h1>
            <?= Html::label(Yii::t('app', 'Select file'), 'file') ?>
            <input name="file" type="file" @change="handleFile">
        </form>
    </div>
</div>
