<?php

use common\assets\DataGridAsset;
use common\assets\VueAsset;
use common\assets\XlsxAsset;

/* @var $this \yii\web\View */

$this->registerJsFile('@staticUrl/js/excel-import.js', [
    'depends' => [
        VueAsset::class,
        XlsxAsset::class,
        DataGridAsset::class
    ]
]);

?>
<div id="excel-import-container">
    <form action="" v-if="sheet === null">
        <h1>File import</h1>
        <label for="file">Select file</label>
        <input name="file" type="file" @change="handleFile">
    </form>
    <div id="excel-table" v-else>
        <table id="excel-import-table"
               class="table table-striped table-bordered">
            <tbody>
            <tr v-for="row in sheet" class="excel-import-row">
                <td class="row-status"
                    v-html="getRowStatusHtml(row)"
                >
                </td>
                <td v-for="cell in row.cells"
                    v-html="cell.value"
                    v-bind:class="{ error: cell.hasError, loading: cell.loading }"
                >
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
