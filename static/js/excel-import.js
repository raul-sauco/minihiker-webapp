
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
      content: null,
      client: null,
      row: null
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
      axios.get(url, this.requestHeaders).then(res => {
        if (!res.data || !res.data.length) {
          this.markProgramMatchNotFound(row);
        } else if (res.data.length === 1) {
          this.markProgramMatchFound(row, res.data[0]);
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
        // If there is no ID, mark the cells as inactive
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
     * @param program 
     */
    markProgramMatchFound: function (row, program) {
      row.programId = program.id
      row.program = program;
      this.markCanUploadRow(row);
      [0,1,2].forEach(cell => {
        this.sheet[row.index].cells[cell].status = 'ready';
      });
    },
    /**
     * Display a select with all the programs found and let
     * the user click on one to select it
     * @param row
     * @param programData
     */
    markProgramMatchFoundMultiple: function (row, programData) {
      row.status = 'needs-action';
      row.programId = null;
      row.program = null;
      row.programs = programData;
      [0,1,2,3,4,5].forEach(cell => {
        row.cells[cell].status = 'needs-action';
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
      // If we didn't find the participant, mark row client null
      if (type.name === 'client') {
        row.client = null;
      }
    },
    /**
     * Update the UI to reflect that we did not find
     * a program for this row
     * @param row
     */
    markProgramMatchNotFound: function (row, programData) {
      row.programId = null;
      row.program = null;
      row.programs = programData;
      row.status = 'error';
      [0,1,2].forEach(cell => {
        row.cells[cell].status = 'error';
      });
      [3,4,5].forEach(cell => {
        row.cells[cell].status = 'none';
      });
      // TODO
    },
    /**
     * Mark a row as an info row
     * @param row
     */
    markRowAsInfo: function (row) {
      row.status = 'info-row';
      // Iterate over the old row cells to create the new one
      row.cells.forEach( cell => {
        cell.status = 'inactive'
      });
      return row;
    },
    /**
     * Mark a row as ready
     * @param row
     */
    markRowAsReady: function (row) {
      row.status = 'ready';
      row.cells.forEach(cell => {
        cell.status = 'none';
      });
    },
    /**
     * Mark the row as having encountered some error
     * during upload
     * @param row
     */
    markRowHasUploadError: function (row) {
      row.status = 'upload-error';
      row.cells.forEach(cell => {
        cell.status = 'none';
      });
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
      } else if (row.status === 'error' || row.status === 'upload-error') {
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
      if (cell.status !== 'inactive' && cell.status !== 'none' && cell.status !== 'info') {
        const c = cell.col;
        if (c === 0 || c === 1 || c === 2) {
          if (Mh.debug) { console.debug('Showing program cell info'); }
          this.showProgramInfo(row);
        } else if (this.clientType.kid.cells.includes(c)) {
          this.showClientInfo(row, this.clientType.kid);
        } else if (this.clientType.parent1.cells.includes(c)) {
          this.showClientInfo(row, this.clientType.parent1);
        } else if (this.clientType.parent2.cells.includes(c)) {
          this.showClientInfo(row, this.clientType.parent2);
        } else if (this.clientType.parent3.cells.includes(c)) {
          this.showClientInfo(row, this.clientType.parent3);
        } else if (this.clientType.parent4.cells.includes(c)) {
          this.showClientInfo(row, this.clientType.parent4);
        } else {
          console.warn('Unrecognized cell type', cell);
        }
      } else if (Mh.debug) {
        console.debug(`No details for ${cell.status} cell ${cell.col}:${cell.row}`);
      }
    },
    /**
     * Handle a click on the row status cell
     * @param row
     */
    handleRowStatusCellClick: function (row) {
      if (row.status === 'can-upload') {
        this.uploadRow(row);
      } else if (row.status === 'upload-error') {
        this.showUploadErrorsInModal(row);
      } else if (Mh.debug) {
        console.debug(`Clicked inactive status-cell in row ${row.index}`);
      }
    },
    /**
     * Display more information about a row upload errors in the
     * application modal
     * @param row
     */
    showUploadErrorsInModal: function (row) {
      this.modal = {
        visible: true,
        title: '服务器错误',
        content: 'upload-error',
        row
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
      } else if (row.programs && row.programs.length > 0) {
        this.showSelectProgramModal(row);
      } else {
        this.showNoProgramFoundModal();
      }
    },
    /**
     * Display one program information in the app modal
     * @param row
     */
    showProgramInModal: function (program) {
      if (Mh.debug) {
        console.debug(
          `Showing information for program ${program.id}`
        );
      }
      this.modal = {
        visible: true,
        title: '项目细节',
        content: 'program-info',
        programs: [program]
      };
    },
    /**
     * Show all the matches found in the modal and let the user
     * manually select one
     * @param row
     */
    showSelectProgramModal: function (row) {
      if (Mh.debug) {
        console.debug(
          `Showing select program modal for row ${row.index}`
        );
      }
      this.modal = {
        visible: true,
        title: '选择项目',
        content: 'select-program',
        row: row,
        programs: row.programs
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
        content: 'program-not-found'
      };
    },
    /**
     * Select one of the programs that were possible matches as the
     * correct one and mark the row
     * @param row
     * @param program
     */
    selectProgram: function (row, program) {
      if (Mh.debug) {
        console.debug(`Selected program ${program.id} for row ${row.index}`);
      }
      this.markProgramMatchFound(row, program);
      this.dismissModal();
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
     * @param client
     */
    showClientInModal: function (client) {
      if (Mh.debug) {
        console.debug(
          `Showing information for client ${client.id}`
        );
      }
      this.modal = {
        visible: true,
        title: '客户细节',
        content: 'client-info',
        client: client
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
        content: 'client-not-found'
      };
    },
    /**
     * Display help information in the app modal
     */
    showHelpModal: function () {
      this.modal = {
        title: '帮助页面',
        content: 'help',
        visible: true
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
        content: null,
        client: null,
        row: null,
        programs: null
      };
    },
    /**
     * Allow the user to upload at once all rows marked as
     * can-upload
     */
    uploadAllRows: function () {
      if (Mh.debug) { console.debug('Uploading all rows'); }
      this.sheet.forEach(row => {
        if (row.status === 'can-upload') {
          this.uploadRow(row).then(() => {
            if (Mh.debug) {
              console.debug(`Uploaded row ${row.index}`);
            }
          });
        }
      });
    },
    /**
     * Upload one row of data to the server
     * @param row
     */
    uploadRow: async function (row) {
      if (Mh.debug) { console.debug(`Uploading row ${row.index}`); }
      if (row.status !== 'can-upload') {
        this.modal = {
          visible: true,
          title: '行尚未准备好上载',
          content: 'row-not-ready-to-upload',
          row
        };
        throw new Error (`Row ${row.index} is not ready to upload`);
      }
      row.status = 'loading';
      if (row.client === null) {
        row.client = await this.uploadClient(row);
      }
      if (!row.client) { throw new Error('Missing row Client'); }
      if (!row.programId) { throw new Error('Missing row Program'); }
      const programFamily = await this.uploadProgramFamily(row);
      if (!programFamily) { throw new Error('Missing ProgramFamily'); }
      const programClient = await this.uploadProgramClient(row);
      if (!programClient) { throw new Error('Missing ProgramClient'); }
      const payment = await this.uploadPayment(row);
      if (!payment) {
        this.markRowHasUploadError(row);
        throw new Error(`Payment upload error`);
      }
      row.payment = payment;
      this.markRowAsReady(row);
    },
    /**
     * Create a new client entry in the server for the
     * current row parameter
     * @param row
     * @returns {Promise<void>}
     */
    uploadClient: async function (row) {
      if (!row.family) {
        row.family = await this.uploadFamily(row);
      }
      const url = this.url + 'clients';
      const data = {
        name: row.cells[6].value,
        family_id: row.family.id,
        is_male: row.cells[7].value.trim() === '男',
        family_role_id: 1,
        id_card_number: row.cells[10].value,
        passport_number: row.cells[11].value || null
      };
      return axios.post(url,data,this.requestHeaders).then(res => {
        if (res.status === 201) {
          if (Mh.debug) {
            console.debug(`Created new Client with id ${res.data.id} for row ${row.index}`, res);
          }
          return res.data;
        } else {
          const msg = `Error creating Client for row ${row.index}`;
          console.error(msg, err);
          throw new Error(msg);
        }
      }).catch(err => {
        const msg = `Error creating Client for row ${row.index}`;
        console.error(msg, err);
        throw new Error(msg);
      });
    },
    /**
     * Try to create a new family with the row data
     * @param row
     * @returns {Promise<void>}
     */
    uploadFamily: async function (row) {
      // Find the client's name to create family name
      const index = this.clientType.kid.cells[0];
      const name = row.cells[index].value;
      if (!name) {
        throw new Error(`Could not determine family name for row ${row.index}`);
      }
      const url = this.url + 'families';
      const data = {name: name.substr(0,12)};
      return axios.post(url,data,this.requestHeaders).then(res => {
        if (res.status === 201) {
          if (Mh.debug) {
            console.debug(`Created new Family with id ${res.data.id} for row ${row.index}`, res);
          }
          return res.data;
        } else {
          const msg = `Error creating Family for row ${row.index}`;
          console.error(msg, res);
          throw new Error(msg);
        }
      }).catch(err => {
        const msg = `Error creating Family for row ${row.index}`;
        console.error(msg, err);
        throw new Error(msg);
      });
    },
    /**
     * Check if the program family instance exists in the server and
     * create it if it does not
     * @param row
     * @returns {Promise<void>}
     */
    uploadProgramFamily: async function (row) {
      const url = this.url + 'program-families';
      const data = {
        program_id: row.programId,
        family_id: row.client.family_id,
        cost: +row.cells[4].value,
        final_cost: +row.cells[3].value,
        status: 7,
        remarks: '表格上传数据'
      };
      const geturl = `${url}/${data.program_id}/${data.family_id}`;
      let res = await axios.get(geturl, this.requestHeaders);
      if (res.data) {
        return res.data;
      }
      // The program family does not exist, upload it
      res = await axios.post(url, data, this.requestHeaders);
      if (!res.data) {
        throw new Error(`Error uploading program family for row ${row.index}`);
      }
      return res.data;
    },
    /**
     * Check if the program client instance exists in the server
     * and create it if it does not
     * @param row
     * @returns {Promise<void>}
     */
    uploadProgramClient: async function (row) {
      const url = this.url + 'program-clients';
      const data = {
        program_id: row.programId,
        client_id: row.client.id,
        status: 7,
        remarks: '表格上传数据'
      };
      const geturl = `${url}/${data.program_id}/${data.client_id}`;
      let res = await axios.get(geturl, this.requestHeaders);
      if (res.data) {
        return res.data;
      }
      // No program-client found, post data to create a new one
      res = await axios.post(url, data, this.requestHeaders);
      if (!res.daat) {
        throw new Error(`Error uploading program client for row ${row.index}`);
      }
      return res.data;
    },
    /**
     * Upload a new payment to the server
     * @param row
     * @returns {Promise<*>}
     */
    uploadPayment: async function (row) {
      const url = this.url + 'payments';
      const data = {
        family_id: row.client.family_id,
        amount: +row.cells[3].value,
        date: new Date().toJSON().substr(0,10),
        program_id: row.programId,
        remarks: '表格上传数据'
      };
      return axios.post(url, data, this.requestHeaders).then(res => {
        if (Mh.debug) {
          console.debug(`Uploaded new payment for row ${row.index}`, res);
        }
        return res.data;
      }).catch(err => {
        if (Mh.debug) {
          console.error(`Payment upload failed`, err);
        }
        row.error = err.message;
        return null;
      });
    },
  },  // End of Vue methods
});
