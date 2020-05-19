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
        <div class="btn-group excel-import-actions"
             v-bind:class="{ blur: modal.visible }">
            <?= Html::button(
                    Html::icon('upload') . ' ' .
                    Yii::t('app', 'Upload all'), [
                        'class' => 'btn btn-success',
                        'v-on:click' => 'uploadAllRows'
                ]
            ) ?>
            <?= Html::button(
                Html::icon('question-sign'), [
                    'class' => 'btn btn-primary',
                    'v-on:click' => 'showHelpModal',
                    'title' => Yii::t('app', 'Help page')
                ]
            ) ?>
        </div>
        <div id="excel-table" v-bind:class="{ blur: modal.visible }">
            <table id="excel-import-table"
                   class="table table-striped table-bordered">
                <tbody>
                <tr v-for="row in sheet" class="excel-import-row">
                    <td class="row-status"
                        v-if="row.status === 'can-upload'">
                        <button class="btn btn-success btn-xs"
                                v-on:click="uploadRow(row)">
                        <span class="glyphicon glyphicon-upload">
                        </span>
                        </button>
                    </td>
                    <td v-else
                        class="row-status"
                        v-bind:class="row.status"
                        v-html="getRowStatusHtml(row)">
                    </td>
                    <td v-for="cell in row.cells"
                        v-html="row.index === 1 ? cell.col + '-' + cell.value : cell.value"
                        v-bind:class="cell.status"
                        v-on:click="showCellDetails(cell, row)">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="v-modal" v-if="modal.visible">
            <div class="v-modal-mask"
                 v-on:click="dismissModal"></div>
            <div class="v-modal-dialog">
                <div class="v-modal-title" v-html="modal.title"></div>
                <div class="v-modal-content" v-html="modal.content"></div>
            </div>
        </div>
    </div>
    <div class="form-container" v-else>
        <form>
            <h1><?= Yii::t('app', 'Spreadsheet upload') ?></h1>
            <?= Html::label(Yii::t('app', 'Select file'), 'file') ?>
            <input name="file" type="file" @change="handleFile">
        </form>
    </div>
</div>
