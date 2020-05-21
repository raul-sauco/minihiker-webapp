<?php
?>

<div id="excel-table" :class="{ blur: modal.visible }">
    <table id="excel-import-table"
           class="table table-striped table-bordered">
        <tbody>
        <tr v-for="row in sheet" class="excel-import-row">
            <td class="row-status"
                :class="row.status"
                @click="handleRowStatusCellClick(row)">
                <div v-if="row.status === 'loading'"
                    v-html="spinner">
                </div>
                <span v-else class="glyphicon"
                      :class="getGlyphiconClass(row)"
                ></span>
            </td>
            <td v-for="cell in row.cells"
                v-html="cell.value"
                :class="cell.status"
                @click="showCellDetails(cell, row)">
            </td>
        </tr>
        </tbody>
    </table>
</div>
