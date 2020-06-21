let exportTableConfig = {
  filename: '项目表格',
  bootstrap: true,
  position: 'top',
  ignoreCols: []
};
const exportTable = TableExport(
  document.getElementById("program-export-table"),
  exportTableConfig
);
const $checkboxes = $('.toggle-column-checkbox');
// Check if some checkboxes are not checked
$checkboxes.each(function () {
  const $c = $(this);
  if (!$c.prop('checked')) {
    updateExportColumnStatus($c);
  }
});

$checkboxes.change(function () {
  updateExportColumnStatus($(this));
});

/**
 * Update the status of the column based on the checkbox status.
 * @param $checkbox
 */
function updateExportColumnStatus($checkbox) {
  const col = +$checkbox.attr('data-column-index');
  const cellClass = '.program-view-client-' + $checkbox.attr('data-attr') + '-cell';
  if ($checkbox.prop('checked')) {
    if (Mh.debug) { console.debug(`Select column ${col}`); }
    $(cellClass).removeClass('table-export-ignore');
    // Remove the index from the config ignored columns
    exportTableConfig.ignoreCols = exportTableConfig.ignoreCols.filter(
      i => i !== col
    );
  } else {
    if (Mh.debug) { console.debug(`Unselect column ${col}`); }
    $(cellClass).addClass('table-export-ignore');
    exportTableConfig.ignoreCols.push(col);
  }
  exportTable.update(exportTableConfig);
}
