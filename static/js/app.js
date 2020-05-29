/**
 * Main Javascrip file for www.minihiker.com
 */

/**
 * Return the role ID for the textual family role
 * @param role
 * @returns {number}
 */
Mh.methods.getFamilyRoleId = (role) => {
  const roles = {
    1: '孩子',
    2: '父亲 爸爸',
    3: '母亲  妈妈',
    4: '爷爷',
    5: '奶奶',
    6: '姥姥',
    7: '姥爷'
  };
  // If not found return role 'other'
  let id = 8;
  Object.keys(roles).forEach(i => {
    if (roles[i].includes(role)) {
      id = i;
    }
  });
  return id;
}

/**
 * Format a string as a datestring ready to be sent to
 * the backend
 * @param date
 * @returns {string|*}
 */
Mh.methods.formatDate = (date) => {
  if (!date) {
    return null;
  }
  if (date.match(/^\d{4}-([0]\d|1[0-2])-([0-2]\d|3[01])$/)) {
    return date;
  }
  const year = date.substring(0, 4);
  const month = date.substring(4, 6);
  const day = date.substring(6, 8);
  const result = `${year}-${month}-${day}`;
  if (!result.match(/^\d{4}-([0]\d|1[0-2])-([0-2]\d|3[01])$/)) {
    console.error(`Failed to convert date ${date}`);
    return null;
  }
  return result;
}
/**
 * Toggles the relation between a Program and a Client.
 *
 * @param button The button that the user clicked to trigger the event.
 * @returns
 */
function manageProgramClient(button) {

  if (button.hasClass('add-client-btn')) {

    addClient(
      button.attr('data-create-url'),
      button.attr('data-program-id'),
      button.attr('data-client-id')
    );
  } else if (button.hasClass('remove-client-btn')) {

    removeClient(
      button.attr('data-delete-url'),
      button.attr('data-program-id'),
      button.attr('data-client-id')
    );
  }

}

/**
 * Add a client to a program.
 *
 * @param url
 * @param programId
 * @param clientId
 */
function addClient(url, programId, clientId) {

  $.post(
    url,
    {
      program_id: programId,
      client_id: clientId
    },
    function (data) {
      var response = JSON.parse(data);

      updateLink('.add-client-btn', response.link_text,
        response.program_id, response.client_id);

      displayNotification(response.message, 'success');

    }).fail(
    function (xhr) {
      console.log('addClient() Received 500 response from server.');
      console.log('There was an error with message: '
        + xhr.responseText);
    }
  );
}

/**
 * Remove a client from a program.
 *
 * @param url
 * @param programId
 * @param clientId
 */
function removeClient(url, programId, clientId) {

  $.post(
    url,
    {
      program_id: programId,
      client_id: clientId
    },
    function (data) {

      var response = JSON.parse(data);

      updateLink('.remove-client-btn', response.link_text,
        response.program_id, response.client_id);

      var details = 'p' + response.program_id + 'c' + response.client_id;

      displayNotification(response.message, 'success', details);

    }).fail(
    function (xhr) {
      console.error('removeClient() Received 500 response from server.');
      console.error('There was an error with message: '
        + xhr.responseText);

      displayNotification(xhr.responseText, 'error');
    }
  );
}

/**
 * Updates a link based on the parameters passed to the function.
 *
 * @param css_class
 * @param text
 * @param program_id
 * @param client_id
 * @returns
 */
function updateLink(css_class, text, program_id, client_id) {
  var query_string = '' + css_class + '[data-program-id=' + program_id
    + '][data-client-id=' + client_id + ']';

  if (css_class == '.add-client-btn') {

    var link_text = $(query_string).attr('data-remove-link-text');

    $(query_string)
      .removeClass('add-client-btn btn-success')
      .addClass('remove-client-btn btn-danger')
      .html(link_text);
  } else if (css_class == '.remove-client-btn') {

    var link_text = $(query_string).attr('data-add-link-text');

    $(query_string)
      .removeClass('remove-client-btn btn-danger')
      .addClass('add-client-btn btn-success')
      .html(link_text);
  } else {
    console.error('Unrecognized css class ' + css_class + ' on updateLink(...)');
  }
}

/**
 * Displays a notification on the screen to inform the user of something
 * that needs their immediate attention.
 *
 * @param text The text to display.
 * @param type The type of notification ('error' , 'success'...)
 * @param details It will be used to generate the tag id property.
 * @returns
 */
function displayNotification(text, type, details) {

  var container = $('#notification-window');
  var previous_content = container.html();
  var css_class = 'notification-message alert ';

  if (type == 'error') {
    css_class += ' alert-danger ';
  } else if (type == 'success') {
    css_class += ' alert-success';
  } else {
    console.warn('Unrecognized message type ' + type + ' at displayNotification()');
  }

  var html = '<div class="' + css_class + '" id="' + details + '">' + text + '</div>';

  container.html(html);

  var id_selector = '#' + details;

  $(id_selector).slideToggle().delay(800).fadeOut(800);
}

/* ***************************************************************************
 * 								CLIENT
 *************************************************************************** */

/**
 * Add a focus out event handler to the name_zh field on the client/form.
 * @returns
 */
$('#client-name_zh').focusout(function () {

  let url = $(this).attr('data-url'),
    name = $(this).val();

  $.get(
    url, 								// URL
    {'name': name},					// Data
    function (data) {

      if (data !== 0) {

        // Return error if name already in DB
        notifyRepeatedName(data);

      }

    },									// Success handler
    'text'								// Response data type
  ).fail(function () {

    $clientNameInput = $('#client-name_zh');

    console.error(
      "An error ocurred sending ajax request to " +
      $clientNameInput.attr('data-url') +
      " with parameters name: " +
      $clientNameInput.val());

  });
});

/**
 * Informs the user that the Chinese name they have choosen for the client
 * already exists on the database.
 * @returns
 */
function notifyRepeatedName(data) {

  const $clientNameInput = $('div.field-client-name_zh input#client-name_zh');

  $('div.field-client-name_zh').removeClass('has-success').addClass('has-warning');
  $clientNameInput.after(data);

  // Attatch a focus in handler that deletes the warning
  $clientNameInput.focusin(function () {

    $('#duplicate-name-warning').remove();

  });
}

/**
 * Display the "family_role_other" field conditionally if role is "other"
 */
$('select#family-role-dropdown').on('change', function () {
  if (+this.value === 8) {
    $('div#family-role-other-container').slideDown();
  } else {
    $('div#family-role-other-container').slideUp();
  }
});

/* ***************************************************************************
 * 								PROGRAM
 *************************************************************************** */

/**
 * Calculate the overall financial data for a program.
 */
function calculateOverallFinancial()
{
  const formatter = new Intl.NumberFormat('zh-CN', {
      style: 'currency',
      currency: 'CNY',
    }),
    $displayDue = $('#program-view-overview-due'),
    $displayPaid = $('#program-view-overview-paid'),
    $displayBalance = $('#program-view-overview-balance');

  let due = 0, paid = 0, balance = 0;

  // Add up all the values in the data-attrs
  $('.due-cell').each( (index, value) => {
    due +=  +$(value).attr('data-due');
  });

  $('.paid-cell').each( (index, value) => {
    paid +=  +$(value).attr('data-paid');
  });

  $('.balance-cell').each( (index, value) => {
    balance +=  +$(value).attr('data-balance');
  });

  // Display the resulting values
  $displayDue.html(formatter.format(due));
  $displayPaid.html(formatter.format(paid));
  $displayBalance.html(formatter.format(balance));
}

/* ***************************************************************************
 * 								PROGRAM FAMILY
 *************************************************************************** */

/**
 * Update the financial status of the ProgramFamily relation based
 * on the current values found on the fields.
 */
function updateFinalCost() {

  // Get a pointer to the HTML elements and calculate values
  const $balanceDisplay = $('td#program-family-overview-balance'),
    cost = Math.abs($('input#programfamily-cost').val()) || 0,
    discount = Math.abs($('input#programfamily-discount').val()) || 0,
    totalCost = cost - discount,
    balance = $balanceDisplay.attr('data-total-paid') - totalCost;

  // Update the UI and input values on the form
  $('input#programfamily-final_cost').val(totalCost);
  $('td#program-family-overview-final-cost').html(formatAsCurrency(totalCost));

  $balanceDisplay.html(formatAsCurrency(balance));

  if (balance < 0) {

    $balanceDisplay.addClass('financial-negative-balance');

  } else {

    $balanceDisplay.removeClass('financial-negative-balance');

  }

}

/**
 * Use the built-in Intl object to format numeric values to currency.
 *
 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/NumberFormat
 *
 * @param amount
 * @returns {string}
 */
function formatAsCurrency(amount) {

  // Create our number formatter.
  const formatter = new Intl.NumberFormat('zh-CN', {
    style: 'currency',
    currency: 'CNY',
    minimumFractionDigits: 2,
    // the default value for minimumFractionDigits depends on the currency  and is usually already 2
  });

  return formatter.format(amount);

}

/* ***************************************************************************
 * 					     	PROGRAM PRICE
 *************************************************************************** */

/**
 * Fetch all the Prices for a given program.
 *
 * @param id
 */
function fetchProgramPrices(id) {

  const url = Mh.globalData.apiurl + 'program-prices?program-id=' + id,
    authHeader = 'Bearer ' + userAccessToken,
    container = $('#program-prices-container');
  let html = '';

  $.ajax({
    url: url,
    type: "GET",
    dataType: 'json',
    // xhrFields: { withCredentials: true },
    success: res => {
      // If successful will get an array of prices
      res.forEach(p => {
        html += generateProgramPriceHtmlTag(p);
      });
      container.html(html);
    },
    fail: res => {
      console.warn(res);
    },
    beforeSend: xhr => {
      xhr.setRequestHeader('Authorization', authHeader);
    }
  });

}

/**
 * Generate the html tag for a program price.
 *
 * @param p
 * @returns {string}
 */
function generateProgramPriceHtmlTag(p) {

  let html = '<div class="program-price-container" id="program-price-' + p.id + '"' +
    ' data-program-price-id="' + p.id + '" data-program-id="' + p.program_id + '">';

  // Program price details
  html += '<div class="program-price-details">';
  html += p.adults + '大 ' + p.kids + '小 ';
  html += parseInt(p.membership_type, 10) === 1 ? '会员 ' : '非会员 ';
  html += p.price + '元';
  html += '</div>';

  // Update button
  html += '<button class="btn btn-xs btn-primary update-program-price-button" ' +
    'onclick="showProgramPriceUpdateForm(' +
    p.id + ',' + p.adults + ',' + p.kids + ',' + p.membership_type + ',' + p.price +
    ')" type="button">更新</button>';

  // Delete button
  html += '<button class="btn btn-xs btn-danger delete-program-price-button" ' +
    'onclick="deleteProgramPrice(' +
    p.id + ')" type="button">删除</button>';

  html += '</div>';


  return html;
}

/**
 * Load the ProgramPrice form, from the webapp server, and
 * display it on the application modal.
 *
 * @param id
 * @param adults
 * @param kids
 * @param membership_type
 * @param price
 */
function showProgramPriceUpdateForm(id, adults, kids, membership_type, price) {

  let modal = $('#appModal');
  let modalContent = $('#modalContent');

  // Get the update url from the container and replace the id, view program/_form #program-prices-container
  let updateUrl = $('#program-prices-container').attr('data-update-url').replace(/0/gi, id);

  $('#modalHeader').html('更新程序价格');
  modalContent.html(
    '<div id="program-price-form-loading-container"><img src="' + baseurl + 'img/loading.gif " /></div>'
  );

  modal.modal('show');

  $.get(updateUrl, res => {
    $('#modalContent').append(res);
    $('#program-price-form-loading-container').hide();
  });
}

/**
 * Load and display the program-price create form.
 */
function showProgramPriceCreateForm(pid) {

  console.log('Fetching create form for program pid:' + pid + ', id: ' + currentProgramId);

  let modal = $('#appModal');
  let modalContent = $('#modalContent');

  // Get the update url from the container and replace the id, view program/_form #program-prices-container
  let createUrl = $('#program-prices-container').attr('data-create-url') + '?pid=' + currentProgramId;

  $('#modalHeader').html('创建新的计划价格');
  modalContent.html(
    '<div id="program-price-form-loading-container"><img src="' + baseurl + 'img/loading.gif " /></div>'
  );

  modal.modal('show');

  $.get(createUrl, res => {
    modalContent.append(res);
    $('#program-price-form-loading-container').hide();
  });

}

/**
 * Submit the information on the program-price form, it could be an update or
 * a new creation.
 *
 * @param id
 */
function submitProgramPriceForm() {

  let loadingContainer = $('#program-price-form-loading-container');
  let formContainer = $('#program-price-form-container');

  // Display loading gif
  formContainer.hide();
  loadingContainer.show();

  let data = {};
  $('form#program-price-form').serializeArray().map(e => {
    if (e.name !== '_csrf') {
      data[e.name] = e.value;
    }
  });

  let method = 'POST';
  let url = Mh.globalData.apiurl + 'program-prices';
  if (data.id) {
    method = 'PUT';
    url += '/' + data.id;
  }

  let authHeader = 'Bearer ' + userAccessToken;

  $.ajax({
    url: url,
    method: method,
    data: data,
    success: res => {

      // Hide the modal
      $('#appModal').modal('hide');

      // Check if we have an update or create
      if (data.id) {
        let tag = '#program-price-' + data.id;
        $(tag).replaceWith(generateProgramPriceHtmlTag(res));
      } else {
        $('#program-prices-container').append(generateProgramPriceHtmlTag(res));
      }

      // TODO check for errors like 422P
    },
    error: res => {
      console.warn('Error sending program-price request');
      console.warn(res);
      // TODO display error and react
    },
    beforeSend: xhr => {
      xhr.setRequestHeader('Authorization', authHeader);
    }
  })

}

/**
 * Delete a ProgramPrice instance and refresh the UI.
 *
 * @param id
 */
function deleteProgramPrice(id) {

  // Show a confirmation prompt
  const confirmed = confirm('Are you sure you want to delete this item?' + ' ' + id);

  if (confirmed) {

    const $container = $('#program-price-' + id),
      $loadingOverlay = $('<div>', {
        html: loadingOverlaySmall
      });

    // Animate displaying the loading overlay
    $loadingOverlay.hide();
    $container.append($loadingOverlay);
    $loadingOverlay.fadeIn('fast');

    $.ajax({
      url: Mh.globalData.apiurl + 'program-prices/' + id,
      type: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + userAccessToken
      }
    }).done((res, status, xhr) => {

      // Success status for update is 200
      if (xhr.status === 204) {

        // Success deleting, remove the item
        $container.fadeOut('fast', () => {
          $container.remove();
        });

      } else {

        alert('There was an error deleting program price ' + id);

      }

    }).fail(xhr => {

      alert('There was an error deleting program price ' + id);

    });

  }

}

/* ***************************************************************************
 * 								WEAPP
 *************************************************************************** */

$('button#update-weapp-cover-image').click(() => {
  let modal = $('#cover-image-selection-modal');
  let modalBody = $('.modal-body');
  let pgId = $('#update-weapp-cover-image').attr('data-pg-id');
  let requestUrl = Mh.globalData.apiurl + 'images?program-group-id=' + pgId;
  let imgUrl = $('#update-weapp-cover-image').attr('data-url');

  let html = '';

  modal.modal();
  modal.modal('show');

  $.getJSON(requestUrl, res => {
    console.log(res);
    res.forEach(e => {
      html += '<img class="pg-images-preview" src="' + imgUrl + e.name + '" data-name="' + e.name + '"/>'
    });

    // Update the modal body with the images
    modalBody.html(html);

    // Add a click handler to the images
    $('.pg-images-preview').click(function () {

      let imgName = $(this).attr('data-name');
      let imgSrc = imgUrl + imgName;

      modal.modal('hide');

      // Update the values on the form
      $('input#programgroup-weapp_cover_image').val(imgName);
      $('img#pg-weapp-cover-image').attr('src', imgSrc);
    });
  });

});

$('#download-images-button').click(function() {
  const $this = $(this);
  $this.html(Mh.globalData.spinner20);
  $this.removeClass('btn-success').addClass('btn-primary');
  const pgId = $this.attr('data-pg-id');
  const url = Mh.globalData.apiurl + 'program-group-image-downloads/' + pgId;
  $.ajax({
    url,
    method: 'PATCH',
    headers: Mh.globalData.authHeaders
  }).done((res, status, xhr) => {
    $this.html('完成了');
    $this.removeClass('btn-primary').addClass('btn-success');
    location.reload();
  }).fail(xhr => {
    $this.html('下载错误');
    $this.removeClass('btn-primary').addClass('btn-danger');
  });
});

/**
 * Fetch
 * @param id
 */
function getProgramGroupImagesPreview(id) {

  let url = Mh.globalData.apiurl + 'bu?program-group-id=' + id;
  let fu = $('#imageuploadform-file-fileupload');

  $.getJSON(url, (res) => {

    // TODO check for errors

    fu.fileupload('option', 'done').call(fu, $.Event('done'), {result: {files: res}});
    displayProgramGroupImageTags();

  });

}

/**
 * Complete the initialization of the Blueimp file upload widget by displaying the
 * images on the first cell of their row.
 */
function displayProgramGroupImageTags() {

  let rows = $('#imageuploadform-file-fileupload .files tr');

  rows.each(function (index) {

    let cells = $(this).children();
    let firstCell = cells.eq(0);
    let link = cells.eq(1).find('a');
    let url = link.attr('href');
    let name = link.attr('title');
    let html = '<span class="preview"><a href="' + url + '" title="' + name + '' +
      '" download="' + name + '"><img src="' +
      url + '" alt="' + name + '"></a></span>';
    firstCell.html(html);
  });

}
