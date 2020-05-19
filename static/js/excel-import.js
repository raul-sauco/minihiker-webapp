
const app = new Vue({
  el: '#excel-import-container',
  data: {
    url: Mh.globalData.apiurl,
    spinner: Mh.globalData.spinner20,
    g: Mh.globalData,
    requestHeaders: {
      headers: {
        'content-type': 'application/json',
        'Authorization': `Bearer ${Mh.globalData.accesstoken}`
      }
    },
    columns: 36,
    sheet: null,
    modal: {
      visible: false,
      title: null,
      content: null
    },
    clientType: {
      kid: {
        name: 'client',
        cells: [6,7,8,9,10,11,12],
        idCell: 10
      },
      parent1: {
        name: 'parent1',
        cells: [13,14,15,16,17,18,19],
        idCell: 17
      },
      parent2: {
        name: 'parent2',
        cells: [20,21,22,23,24,25],
        idCell: 23
      },
      parent3: {
        name: 'parent3',
        cells: [26,27,28,29,30],
        idCell: 29  // todo this is passport, change to id
      },
      parent4: {
        name: 'parent4',
        cells: [31,32,33,34,35],
        idCell: 34  // todo this is passport, change to id
      }
    }
  },
  mounted () {
    if (Mh.debug) { console.debug('excel-import.js mounted'); }
  },
  methods: {
    /**
     * Handle a file selected by the user
     * @param e
     */
    handleFile: function (e) {
      const files = e.target.files, f = files[0];
      const reader = new FileReader();
      reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, {
          type: 'array',
          dates: true
        });

        if (Mh.debug) {
          const sheetCount = workbook.SheetNames.length;
          console.debug('Workbook has ' + sheetCount + ' sheet/s');
          // TODO notify the user that we only work with one sheet
        }

        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const sheetJson = XLSX.utils.sheet_to_json(sheet, {
          header:1,
          raw: false
        });

        this.addSheetData(sheetJson);

      };
      reader.readAsArrayBuffer(f);

    },
    /**
     * Add the data extracted from the excel sheet[0] to the
     * Vue object
     * @param sheetJson
     */
    addSheetData: function (sheetJson) {
      const rows = [];
      let r = 0;
      sheetJson.forEach( rowJson => {
        const rowContent = this.generateRow(rowJson, r);
        if (rowContent !== null) {
          rows.push(rowContent);
          r++;
        }
      });
      this.sheet = rows;
    },
    /**
     * This function converts each cell in a row from
     * data to an object with Mh information.
     * @param rowJson
     * @param rowIndex the 0 index of the row in the sheet
     * @returns {*}
     */
    generateRow: function (rowJson, rowIndex) {
      let cells = [];
      let empty = true;
      for (let col = 0; col < this.columns; col++) {
        // If any of the cells has content, mark the row
        if (rowJson[col]) {
          empty = false;
        }
        cells.push({
          row: rowIndex,
          col,
          value: rowJson[col],
          status: 'loading'
        });
      }
      const row = {
        index: rowIndex,
        status: 'loading',
        cells
      };
      if (empty) {
        return null;
      } else {
        return this.processRow(row);
      }
    },
    /**
     * Process the row to determine what to do with it
     * @param row
     */
    processRow: function (row) {
      const infoMessages = [
        'CRM上传数据模板','项目名称*','生日*','护照号','项目开始时间*','项目结束时间*',
        '项目收款*','备注'
      ];

      if (infoMessages.includes(row.cells[0].value)) {
        return this.markRowAsInfo(row);
      } else {
        this.fetchRowData(row);
        return row;
      }
    },
    /**
     * Try to find server data associated with this row
     * @param row
     */
    fetchRowData: function (row) {
      this.fetchRowProgram(row);
      this.fetchRowClients(row);
    },
    /**
     * Try to match row data to an existing program in the
     * server
     * @param row
     */
    fetchRowProgram: function (row) {
      const url = this.url + 'program-search?' +
        `name=${row.cells[0].value}` +
        `&start-date=${row.cells[1].value}` +
        `&end-date=${row.cells[2].value}` +
        '&expand=programGroup.type';
      axios.get(url, {
        headers: {
          'content-type': 'application/json',
          'Authorization': `Bearer ${this.g.accesstoken}`
        }
      }).then(res => {
        if (!res.data || !res.data.length) {
          this.markProgramMatchNotFound(row);
        } else if (res.data.length === 1) {
          this.markProgramMatchFound(row, res.data);
        } else {
          this.markProgramMatchFoundMultiple(row, res.data);
        }
      }).catch(err => {
        this.markProgramMatchNotFound(row);
        console.error(err);
      });
    },
    /**
     * Find data in the server to match client's data in the
     * spreadsheet
     * @param row
     */
    fetchRowClients: function (row) {
      // Fetch participant
      this.fetchRowClient(row, this.clientType.kid);
      this.fetchRowClient(row, this.clientType.parent1);
      this.fetchRowClient(row, this.clientType.parent2);
      this.fetchRowClient(row, this.clientType.parent3);
      this.fetchRowClient(row, this.clientType.parent4);
    },
    /**
     * Try to find one client in the server given it's ID
     * @param row
     * @param type the type of client to fetch for
     */
    fetchRowClient: function (row, type) {
      const id = row.cells[type.idCell].value;
      // Only fetch for cells that have an ID value
      if (id) {
        const url = this.url + 'client-search?expand=familyName&' +
          `id=${id}`;
        axios.get(url, this.requestHeaders).then(res => {
          if (!res.data || !res.data.length) {
            this.markClientNotFound(row, type);
          } else if (res.data.length === 1) {
            this.markClientMatchFound(row, res.data[0], type);
          }
        }).catch(err => {
          console.error(err);
          this.markClientNotFound(row, type);
        });
      } else {
        if (Mh.debug) {
          console.debug(
            `No ID value for row ${row.index} type ${type.name}`
          );
        }
        // If there is no ID, mark the rows as inactive
        type.cells.forEach(cell => {
          row.cells[cell].status = 'inactive';
        });
      }
    },
    /**
     * Check if we have enough information to mark the
     * row as uploadable.
     * @param row
     */
    markCanUploadRow: function (row) {
      // We need a program and we need to wait for the client request
      if (row.program && (row.client || row.client === null)) {
        row.status = 'can-upload';;
        [3,4,5].forEach(cell => {
          row.cells[cell].status = 'can-upload';
        });
      }
    },
    /**
     * Update the UI to reflect that we found a
     * program for this row
     * @param row
     * @param programData JSON program data
     */
    markProgramMatchFound: function (row, programData) {
      row.programId = programData[0].id
      row.program = programData[0];
      this.markCanUploadRow(row);
      [0,1,2].forEach(cell => {
        this.sheet[row.index].cells[cell].status = 'ready';
      });
    },
    /**
     * TODO; display a select with all the programs found and let
     * the user click on one to select it
     * @param row
     * @param programData
     */
    markProgramMatchFoundMultiple: function (row, programData) {
      console.debug('Mark row as found multiple', row);
      this.sheet[row.index].status = 'needs-action';
      [0,1,2].forEach(cell => {
        this.sheet[row.index].cells[cell].status = 'needs-action';
      });
    },
    /**
     * Mark the given client's cells in the row as found
     * @param row
     * @param clientData JSON client data
     * @param type the type of client found
     */
    markClientMatchFound: function (row, clientData, type) {
      if (type.name === 'client') {
        // We found ourselves the participant
        if (Mh.debug) {
          console.debug(
            `Found row ${row.index} participant, id: ${clientData.id}`
          );
        }
        row.clientId = clientData.id
        row.client = clientData;
        this.markCanUploadRow(row);
      } else {
        row[type.name] = clientData;
      }

      // Update cell status
      type.cells.forEach(cell => {
        row.cells[cell].status = 'ready';
      });
    },
    /**
     * Mark the given client's cells in the row as not found
     * @param row the row object
     * @param type the type of client
     */
    markClientNotFound: function (row, type) {
      // If we couldn't find a client, show that we will create it
      type.cells.forEach(cell => {
        row.cells[cell].status = 'can-upload';
      });
    },
    /**
     * Update the UI to reflect that we did not find
     * a program for this row
     * @param row
     */
    markProgramMatchNotFound: function (row, programData) {
      this.sheet[row.index].programId = null;
      this.sheet[row.index].program = null;
      this.sheet[row.index].programs = programData;
      console.debug('Mark row as not found program', row);
      this.sheet[row.index].status = 'error';
      [0,1,2].forEach(cell => {
        this.sheet[row.index].cells[cell].status = 'error';
      });
      [3,4,5].forEach(cell => {
        this.sheet[row.index].cells[cell].status = 'none';
      });
      // TODO
    },
    /**
     * Mark a row as an info row
     * @param row
     */
    markRowAsInfo: function (row) {
      const newRow = {
        index: row.index,
        status: 'info-row',
        cells: []
      }
      // Iterate over the old row cells to create the new one
      row.cells.forEach( cell => {
        newRow.cells.push({
          row: cell.rowIndex,
          col: cell.col,
          value: cell.value,
          hasError: false,
          loading: false,
          errorMessage: null
        });
      });
      return newRow;
    },
    /**
     * Get the html content that we should display in the
     * status cell of a row
     * @param row
     * @returns {string}
     */
    getRowStatusHtml: function (row) {
      if (row.status === 'loading') {
        return this.spinner;
      } else if (row.status === 'info-row') {
        return '<span class="glyphicon glyphicon-info-sign"></span>'
      } else if (row.status === 'error') {
        return '<span class="glyphicon glyphicon-remove"></span>';
      } else if (row.status === 'needs-action') {
        return '<span class="glyphicon glyphicon-warning-sign"></span>'
      } else if (row.status === 'ready') {
        return '<span class="glyphicon glyphicon-ok"></span>'
      }
    },
    /**
     * Display more information about the current state of the
     * cell and row
     * @param cell
     * @param row
     */
    showCellDetails: function (cell, row) {
      console.log(cell);
      console.log(row);
      const c = cell.col;
      if (c === 0 || c === 1 || c === 2) {
        if (Mh.debug) { console.debug('Showing program cell info'); }
        this.showProgramInfo(row);
      } else if (this.clientType.kid.cells.includes(c)) {
        if (Mh.debug) { console.debug('Showing participant cell info'); }
        this.showClientInfo(row, this.clientType.kid);
      } else if (this.clientType.parent1.cells.includes(c)) {
        if (Mh.debug) { console.debug('Showing parent1 cell info'); }
        this.showClientInfo(row, this.clientType.parent1);
      } else {
        console.warn('Unrecognized cell type', cell);
      }
    },
    /**
     * Display a modal with info about the program that
     * matches the row data
     * @param row
     */
    showProgramInfo: function(row) {
      if (row.program && row.programId) {
        this.showProgramInModal(row.program);
      } else {
        this.showNoProgramFoundModal();
      }
    },
    /**
     * Display one program information in the app modal
     * @param p
     */
    showProgramInModal: function (p) {
      if (Mh.debug) {
        console.debug(
          `Showing information for program ${p.id}`
        );
      }
      const link = `${this.g.baseurl}program/${p.id}`;
      const content =
        `<div>链接: <a href="${link}" target="_blank">${p.id}</a></div>` +
        `<div>项目: ${p.programGroup.name}</div>` +
        `<div>地点: ${p.programGroup.location_id}</div>` +
        `<div>类型: ${p.programGroup.type.name}</div>` +
        `<div>开始时间: ${p.start_date}</div>` +
        `<div>结束日期: ${p.end_date}</div>`;
      this.modal = {
        visible: true,
        title: '项目细节',
        content
      };
    },
    /**
     * Display a modal message to inform that we did not find
     * any programs that matched the parameters
     */
    showNoProgramFoundModal: function () {
      this.modal = {
        visible: true,
        title: '未找到项目',
        content: '给定Excel工作表中的数据，我们找不到任何合适的项目'
      };
    },
    /**
     * Evaluate what info to display for this cell in the
     * app modal
     * @param row
     * @param type
     */
    showClientInfo: function (row, type) {
      // Check if we found info for this client on the server
      const clientData = row[type.name];
      if (clientData) {
        this.showClientInModal(clientData);
      } else {
        this.showNoClientFoundModal();
      }
    },
    /**
     * Show one client's information in the app modal
     * @param c
     */
    showClientInModal: function (c) {
      if (Mh.debug) {
        console.debug(
          `Showing information for client ${c.id}`
        );
      }
      const link = `${this.g.baseurl}client/${c.id}`;
      const familyLink = `${this.g.baseurl}family/${c.family_id}`;
      const content =
        `<div>链接: <a href="${link}" target="_blank">${c.id}</a></div>` +
        `<div>名称: ${c.name_zh}</div>` +
        `<div>身份证号码: ${c.id_card_number}</div>` +
        `<div>家庭: <a href="${familyLink}" target="_blank">${c.familyName}</a></div>`;
      this.modal = {
        visible: true,
        title: '客户细节',
        content
      };
    },
    /**
     * Display a modal to inform that we did not find
     * any clients that matched the parameters
     */
    showNoClientFoundModal: function () {
      this.modal = {
        visible: true,
        title: '未找到客户',
        content: '给定Excel工作表中的数据，我们找不到任何合适的客户'
      };
    },
    /**
     * Dismiss the application modal
     */
    dismissModal: function () {
      if (Mh.debug) { console.debug('Dismissing modal'); }
      this.modal = {
        visible: false,
        title: null,
        content: null
      };
    },
    /**
     * Upload one row of data to the server
     * @param row
     */
    uploadRow: function (row) {
      if (Mh.debug) { console.debug(`Uploading row ${row.index}`, row); }
      row.status = 'loading';
      setTimeout(() => {
        row.status = 'ready';
        console.debug(`Row ${row.index} is ready`);
      }, 3000);
    }
  }
});
