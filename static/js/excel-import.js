
const app = new Vue({
  el: '#excel-import-container',
  data: {
    url: Mh.globalData.apiurl,
    spinner: Mh.globalData.spinner20,
    g: Mh.globalData,
    formatDate: Mh.methods.formatDate,
    requestHeaders: {
      headers: {
        'content-type': 'application/json',
        'Authorization': `Bearer ${Mh.globalData.accesstoken}`
      }
    },
    columns: 41,
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
        cells: [20,21,22,23,24,25,26],
        idCell: 24
      },
      parent3: {
        name: 'parent3',
        cells: [27,28,29,30,31,32,33],
        idCell: 31
      },
      parent4: {
        name: 'parent4',
        cells: [34,35,36,37,38,39,40],
        idCell: 38
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
        client: null,
        familyId: null,
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
      const startDate = this.formatDate(row.cells[1].value);
      const endDate = this.formatDate(row.cells[2].value);
      const url = this.url + 'program-search?' +
        `name=${row.cells[0].value}` +
        `&start-date=${startDate}` +
        `&end-date=${endDate}` +
        '&expand=programGroup.type,programPeriod';
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
      // Fetch parents
      [1,2,3,4].forEach(i => {
        this.fetchRowClient(row, this.clientType[`parent${i}`])
      });
    },
    /**
     * Try to find one client in the server given it's ID
     * @param row
     * @param type the type of client to fetch for
     */
    fetchRowClient: async function (row, type) {
      const name = row.cells[type.cells[0]].value;
      const id = row.cells[type.idCell].value;
      const passportIndex = type.idCell + 1;
      const passport = row.cells[passportIndex].value;
      // Only fetch for cells that have a name value
      if (name || id || passport) {
        let url = `${this.url}client-search?expand=familyName&name=${name}`;
        if (id) {
          url += `&id=${id}`;
        }
        if (passport) {
          url += `&passport=${passport}`;
        }
        axios.get(url, this.requestHeaders).then(res => {
          if (!res.data || !res.data.length) {
            this.markClientNotFound(row, type);
          } else if (res.data.length === 1) {
            this.markClientMatchFound(row, res.data[0], type);
          } else {
            // TODO deal with multiple clients
            console.warn(`Error; got ${res.data.length} clients from server`, res.data);
            this.markClientMatchFoundMultiple(row, res.data, type);
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
        row.familyId = clientData.family_id;
        row.clientId = clientData.id
        row.client = clientData;
        this.markCanUploadRow(row);
      } else {
        row[type.name] = clientData;
        // Only set family ID if not set by client already
        if (!row.familyId) {
          row.familyId = clientData.family_id;
        }
      }

      // Update cell status
      type.cells.forEach(cell => {
        row.cells[cell].status = 'ready';
      });
    },
    /**
     * Mark a row's client type as not autoresolved, it needs
     * the user to make a decision.
     * @param row
     * @param data
     * @param type
     */
    markClientMatchFoundMultiple: function(row, data, type) {
      row.status = 'needs-action';
      if (!row.clients) {
        row.clients = {};
      }
      type.cells.forEach(cell => {
        row.cells[cell].status = 'needs-action';
      });
      row.clients[type.name] = data;
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
     * Get the glyphicon class for the status cell
     * @param row
     * @returns {string}
     */
    getGlyphiconClass: function (row) {
      const gly = {
        'can-upload': 'upload',
        'info-row': 'info-sign',
        'error': 'remove',
        'upload-error': 'remove',
        'needs-action': 'warning-sign',
        'ready': 'ok'
      };
      if (!gly[row.status]) {
        return '';
      }
      return 'glyphicon-' + gly[row.status];
    },
    /**
     * Display more information about the current state of the
     * cell and row
     * @param cell
     * @param row
     */
    showCellDetails: function (cell, row) {
      if (cell.status !== 'inactive' &&
          cell.status !== 'none' &&
          cell.status !== 'info') {
        const c = cell.col;
        if (c === 0 || c === 1 || c === 2) {
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
     * @param program
     */
    showProgramInModal: function (program) {
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
      if (row[type.name]) {
        this.showClientInModal(row[type.name]);
      } else if (row.clients && row.clients[type.name]) {
        this.showSelectClientModal(row, type);
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
     * Show all the client matches found for the given type in the
     * modal and let the user select one.
     * @param row
     * @param type
     */
    showSelectClientModal: function (row, type) {
      if (Mh.debug) {
        console.debug(
          `Showing select client modal for row ${row.index} client type ${type.name}`
        );
      }
      this.modal = {
        visible: true,
        title: '选择客户',
        content: 'select-client',
        row: row,
        type: type
      }
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
     * Mark one client as selected out of a few possibilities offered
     * to the user. If the selected client is the participant,
     * try to update the row status.
     * @param row
     * @param type
     * @param client
     */
    selectClient(row, type, client) {
      if (Mh.debug) {
        console.debug(`User selected client ${client.id} "${client.name_zh}"` +
          `for type ${type.name} and row ${row.index}`);
      }
      // Delegate to mark client found function
      this.markClientMatchFound(row, client, type);
      this.dismissModal();
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
      // If we didn't find an existing client, upload it
      if (row.client === null) {
        row.client = await this.uploadClient(row);
      }
      if (!row.client) {
        const msg = '无法上传行客户端数据';
        row.error = msg;
        this.markRowHasUploadError(row);
        throw new Error(msg);
      }
      if (!row.programId) {
        const msg = '缺少行项目ID';
        row.error = msg;
        this.markRowHasUploadError(row);
        throw new Error(msg);
      }
      const programFamily = await this.uploadProgramFamily(row);
      if (!programFamily) {
        const msg = '无法上载项目-家庭数据';
        row.error = msg;
        this.markRowHasUploadError(row);
        throw new Error(msg);
      }
      const programClient = await this.uploadProgramClient(row);
      if (!programClient) {
        const msg = '无法上载项目-客户数据';
        row.error = msg;
        this.markRowHasUploadError(row);
        throw new Error(msg);
      }
      const payment = await this.uploadPayment(row);
      if (!payment) {
        this.markRowHasUploadError(row);
        throw new Error(`Payment upload error`);
      }
      row.payment = payment;
      try {
        const success = await this.uploadParents(row);
      } catch (error) {
        console.error(error);
      }
      this.markRowAsReady(row);
    },
    /**
     * Try to create a new family with the row data
     * @param row
     * @returns {Promise<void>} family JSON
     */
    uploadFamily: async function (row) {
      // Find the client's name to create family name
      const index = this.clientType.kid.cells[0];
      const name = row.cells[index].value + '家庭';
      if (!name) {
        throw new Error(`Could not determine family name for row ${row.index}`);
      }
      const url = this.url + 'families';
      const data = {name: name.substr(0,12)};
      let res;
      try {
        res = await axios.post(url,data,this.requestHeaders);
        if (Mh.debug) {
          console.debug(
            `Created new Family with id ${res.data.id} for row ${row.index}`,
            res);
        }
        return res.data;
      } catch (error) {
        const msg = `Error ${error.response.status} creating Family for row ${row.index}`;
        console.error(msg, error);
        throw new Error(msg);
      }
    },
    /**
     * Create a new client entry in the server for the
     * current row parameter
     * @param row
     * @returns {Promise<void>}
     */
    uploadClient: async function (row) {
      if (!row.familyId) {
        try {
          const family = await this.uploadFamily(row);
          if (family && family.id) {
            row.familyId = family.id;
          } else {
            console.error(
              `Error obtaining row ${row.index} family id`, family);
          }
        } catch (error) {
          throw new Error(`Failed to create family for row ${row.index}`);
        }
      }
      const url = this.url + 'clients';
      const data = {
        name_zh: row.cells[6].value,
        family_id: row.familyId,
        // is_male: row.cells[7].value.trim() === '男',
        family_role_id: 1,
        id_card_number: row.cells[10].value,
        passport_number: row.cells[11].value || null,
        passport_expire_date: this.formatDate(row.cells[12].value)
      };
      if (row.cells[7] && row.cells[7].value) {
        data.is_male = row.cells[7].value.trim() === '男';
      }
      try {
        const res = await axios.post(url,data,this.requestHeaders);
        if (Mh.debug) {
          console.debug(
            `Created new Client with id ${res.data.id} for row ${row.index}`,
            res.data);
        }
        return res.data;
      } catch (error) {
        const msg = `Error ${error.response.status} creating Client ` +
        `for row ${row.index}`;
        console.error(msg, error);
        throw new Error(msg);
      }
    },
    /**
     * Upload parent information to the server
     * @param row
     */
    uploadParents: async function (row) {
      let success = true;
      [1,2,3,4].forEach(async i => {
        const index = `parent${i}`;
        const idCell = this.clientType[index].idCell;
        const id = row.cells[idCell].value;
        if (id && !row[index]) {
          try {
            const parent = await this.uploadOneParent(row, index);
          } catch (error) {
            success = false;
          }
        }
      });
      return success;
    },
    /**
     *
     * @param row
     * @param index
     * @returns {Promise<*>}
     */
    uploadOneParent: async function (row, index) {
      const cells = row.cells;
      const indexes = this.clientType[index].cells;
      const data = {
        family_id: row.familyId,
        name_zh: cells[indexes[0]].value.substr(0,12),
        phone: cells[indexes[1]].value || null,
        wechat: cells[indexes[2]].value || null,
        family_role_id: Mh.methods.getFamilyRoleId(cells[indexes[3]].value),
        id_card_number: cells[indexes[4]].value,
        passport_number: cells[indexes[5]].value || null,
        passport_expire_date: this.formatDate(cells[indexes[6]].value)
      };
      const url = this.url + 'clients';
      try {
        const res = await axios.post(url, data, this.requestHeaders);
        row[index] = res.data;
        if (Mh.debug) {
          console.debug(`Uploaded new ${index} for row ${row.index}`, res.data);
        }
        return res.data;
      } catch (error) {
        const message = `Error ${error.response.status} ` +
          `uploading ${index} for row ${row.index}`;
        console.error(message, error);
        throw new Error(message);
      }
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
        family_id: row.familyId,
        cost: +row.cells[4].value,
        final_cost: +row.cells[3].value,
        status: 7,
        remarks: row.cells[5].value
      };
      const geturl = `${url}/${data.program_id}/${data.family_id}`;
      let res;
      try {
        res = await axios.get(geturl, this.requestHeaders);
      } catch (error) {
        if (error.response.status === 404) {
          if (Mh.debug) {
            console.debug(
              `No ProgramFamily found for ${data.program_id}:${data.family_id}`
            );
          }
        } else {
          console.error(`Error ${error.response.status} trying to read ` +
            `ProgramFamily for row ${row.index}`);
        }
      }
      if (res && res.data) {
        return res.data;
      }
      // The program family does not exist, upload it
      try {
        res = await axios.post(url, data, this.requestHeaders);
        return res.data;
      } catch (error) {
        console.error(
          `Error posting program family data for row ${row.index}`, error);
      }
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
      let res;
      try {
        res = await axios.get(geturl, this.requestHeaders);
      } catch (error) {
        if (error.response.status === 404) {
          if (Mh.debug) {
            console.debug(
              `No ProgramClient found for ${data.program_id}:${data.client_id}`
            );
          } else {
            console.error(`Error ${error.response.status} trying to read ` +
              `ProgramClient for row ${row.index}`);
          }
        }
      }
      if (res && res.data) {
        return res.data;
      }
      // No program-client found, post data to create a new one
      try {
        res = await axios.post(url, data, this.requestHeaders);
        return res.data;
      } catch (error) {
        console.error(
          `Error posting program family data for row ${row.index}`, error);
      }
    },
    /**
     * Upload a new payment to the server
     * @param row
     * @returns {Promise<*>}
     */
    uploadPayment: async function (row) {
      const url = this.url + 'payments';
      const data = {
        family_id: row.familyId,
        amount: +row.cells[3].value,
        date: new Date().toJSON().substr(0,10),
        program_id: row.programId,
        remarks: '表格上传数据'
      };
      try {
        const res = await axios.post(url, data, this.requestHeaders);
        if (Mh.debug) {
          console.debug(`Uploaded new payment for row ${row.index}`, res.data);
        }
        return res.data;
      } catch (error) {
        row.error = error.response.message;
        console.error(
          `Payment upload failed with status ${error.response.status}` +
          ` for row ${row.index}`, error);
        return null;
      }
    },
  },  // End of Vue methods
});
