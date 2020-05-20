<?php
/* @var $this \yii\web\View */
?>

<table class="table table-bordered table-striped" id="example-table">
    <tr class="example-row">
        <td class="loading row-status" v-html="spinner"></td>
        <td class="loading">该行正在加载，需要稍等</td>
    </tr>
    <tr class="example-row">
        <td class="can-upload row-status">
            <button class="btn btn-sm btn-success">
                <span class="glyphicon glyphicon-upload"></span>
            </button>
        </td>
        <td class="ready">该行已准备好上载</td>
    </tr>
    <tr class="example-row">
        <td class="needs-action row-status">
            <span class="glyphicon glyphicon-warning-sign"></span>
        </td>
        <td class="needs-action">此行包含需要您执行一些操作才能上载的单元格</td>
    </tr>
    <tr class="example-row">
        <td class="error row-status">
            <span class="glyphicon glyphicon-remove"></span></td>
        <td class="error">该行包含需要从服务器端手动修复的错误</td>
    </tr>
    <tr class="example-row">
        <td class="info-row row-status">
            <span class="glyphicon glyphicon-info-sign"></span></td>
        <td class="none">该行是信息行，不包含任何数据</td>
    </tr>
    <tr class="example-row">
        <td colspan="2" class="loading">该单元正在加载，需要稍等</td>
    </tr>
    <tr class="example-row">
        <td colspan="2" class="ready">
            该单元格包含服务器上已经存在的数据，我们不需要上传，您可以单击该单元格以
            查看有关我们找到的数据的更多信息。
        </td>
    </tr>
    <tr class="example-row">
        <td colspan="2" class="needs-action">
            此单元格包含需要您执行一些操作才能上传的数据
        </td>
    </tr>
    <tr class="example-row">
        <td colspan="2" class="error">
            该单元格有错误，不允许将数据发送到服务器，您可以单击该单元格以查看更多信息
        </td>
    </tr>
    该单元格包含将上传到服务器的数据
    <tr class="example-row">
        <td colspan="2">
            这些单元格不包含任何可上传的数据
        </td>
    </tr>
</table>
