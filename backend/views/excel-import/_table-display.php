<?php
?>

<div id="excel-table" :class="{ blur: modal.visible }">
    <table id="excel-import-table"
           class="table table-striped table-bordered">
        <tbody>
        <tr v-for="row in sheet" class="excel-import-row">
            <td class="row-status"
                v-if="row.status === 'can-upload'">
                <button class="btn btn-success btn-xs"
                        @click="uploadRow(row)">
                        <span class="glyphicon glyphicon-upload">
                        </span>
                </button>
            </td>
            <td v-else
                class="row-status"
                :class="row.status"
                v-html="getRowStatusHtml(row)">
            </td>
            <td v-for="cell in row.cells"
                v-html="row.index === 1 ? cell.col + '-' + cell.value : cell.value"
                :class="cell.status"
                @click="showCellDetails(cell, row)">
            </td>
        </tr>
        </tbody>
    </table>
</div>
