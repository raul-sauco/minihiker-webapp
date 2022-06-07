/**
 * Main Javascript file for www.minihiker.com
 */

/**
 * Generate a random string of a desired length
 * https://stackoverflow.com/a/1349426/2557030
 * @param length
 * @returns {string}
 */
Mh.methods.generateRandomString = (length = 32) => {
  const characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  const charactersLength = characters.length;
  let result = "";
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
};

/**
 * Return the role ID for the textual family role
 * @param role
 * @returns {number}
 */
Mh.methods.getFamilyRoleId = (role) => {
  const roles = {
    1: "孩子",
    2: "父亲 爸爸",
    3: "母亲  妈妈",
    4: "爷爷",
    5: "奶奶",
    6: "姥姥",
    7: "姥爷",
  };
  // If not found return role 'other'
  let id = 8;
  Object.keys(roles).forEach((i) => {
    if (roles[i].includes(role)) {
      id = i;
    }
  });
  return id;
};

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
};

/**
 * Displays a notification on the screen to inform the user of something
 * that needs their immediate attention. *
 * @param text The text to display.
 * @param type The type of notification ('error' , 'success'...)
 * @param details It will be used to generate the tag id property.
 * @returns
 */
Mh.methods.displayNotification = (text, type, details) => {
  const container = $("#notification-window");
  let css_class = "notification-message alert ";
  if (type === "error") {
    css_class += " alert-danger ";
  } else if (type === "success") {
    css_class += " alert-success";
  } else {
    console.warn(
      "Unrecognized message type " + type + " at displayNotification()"
    );
  }
  // Allow for empty details parameter
  if (!details) {
    // https://stackoverflow.com/a/8084248/2557030
    details = Math.random().toString(36).substring(7);
  }
  const html =
    '<div class="' + css_class + '" id="' + details + '">' + text + "</div>";
  container.html(html);
  $(`#${details}`).slideToggle().delay(2000).fadeOut();
};

/**
 * Convenience method to display success notifications
 * @param text
 * @param details
 */
Mh.methods.flashSuccess = (text, details = "success-notification") => {
  Mh.methods.displayNotification(text, "success", details);
};

/**
 * Convenience method to display error notifications
 * @param text
 * @param details
 */
Mh.methods.flashError = (text, details = "error-notification") => {
  Mh.methods.displayNotification(text, "error", details);
};

/**
 * Send a log message to the server. This method is best-effort and fails silently.
 * @param level
 * @param category
 * @param message
 * @returns {Promise<void>}
 */
Mh.methods.log = async (level = 1, category = "backend-js", message = "") => {
  const data = {
    level,
    category,
    log_time: (Date.now() / 1000) | 0,
    message,
  };
  const url = Mh.globalData.apiurl + "logs";
  const response = await fetch(url, {
    method: "POST", // *GET, POST, PUT, DELETE, etc.
    mode: "cors", // no-cors, *cors, same-origin
    cache: "default", // *default, no-cache, reload, force-cache, only-if-cached
    credentials: "same-origin", // include, *same-origin, omit
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${Mh.globalData.accesstoken}`,
    },
    redirect: "follow", // manual, *follow, error
    referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
    body: JSON.stringify(data), // body data type must match "Content-Type" header
  });
  if (Mh.debug) {
    console.log(await response.json());
  }
};

/**
 * Convenience method to log errors.
 * Internally it calls log(1, ..., ..., ...)
 * @param category
 * @param message
 */
Mh.methods.logError = (category = "backend-js", message = "") => {
  Mh.methods.log(1, category, message);
};

/**
 * Toggles the relation between a Program and a Client.
 *
 * @param button The button that the user clicked to trigger the event.
 * @returns
 */
function manageProgramClient(button) {
  if (button.hasClass("add-client-btn")) {
    addClient(
      button.attr("data-create-url"),
      button.attr("data-program-id"),
      button.attr("data-client-id")
    );
  } else if (button.hasClass("remove-client-btn")) {
    removeClient(
      button.attr("data-delete-url"),
      button.attr("data-program-id"),
      button.attr("data-client-id")
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
      client_id: clientId,
    },
    function (data) {
      var response = JSON.parse(data);

      updateLink(
        ".add-client-btn",
        response.link_text,
        response.program_id,
        response.client_id
      );

      Mh.methods.displayNotification(response.message, "success");
    }
  ).fail(function (xhr) {
    console.log("addClient() Received 500 response from server.");
    console.log("There was an error with message: " + xhr.responseText);
  });
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
      client_id: clientId,
    },
    function (data) {
      var response = JSON.parse(data);

      updateLink(
        ".remove-client-btn",
        response.link_text,
        response.program_id,
        response.client_id
      );

      var details = "p" + response.program_id + "c" + response.client_id;

      Mh.methods.displayNotification(response.message, "success", details);
    }
  ).fail(function (xhr) {
    console.error("removeClient() Received 500 response from server.");
    console.error("There was an error with message: " + xhr.responseText);

    Mh.methods.displayNotification(xhr.responseText, "error");
  });
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
  var query_string =
    "" +
    css_class +
    "[data-program-id=" +
    program_id +
    "][data-client-id=" +
    client_id +
    "]";

  if (css_class == ".add-client-btn") {
    var link_text = $(query_string).attr("data-remove-link-text");

    $(query_string)
      .removeClass("add-client-btn btn-success")
      .addClass("remove-client-btn btn-danger")
      .html(link_text);
  } else if (css_class == ".remove-client-btn") {
    var link_text = $(query_string).attr("data-add-link-text");

    $(query_string)
      .removeClass("remove-client-btn btn-danger")
      .addClass("add-client-btn btn-success")
      .html(link_text);
  } else {
    console.error(
      "Unrecognized css class " + css_class + " on updateLink(...)"
    );
  }
}

/* ***************************************************************************
 * 								CLIENT
 *************************************************************************** */

/**
 * Add a focus out event handler to the name_zh field on the client/form.
 * @returns
 */
$("#client-name_zh").focusout(function () {
  let url = $(this).attr("data-url"),
    name = $(this).val();

  $.get(
    url, // URL
    { name: name }, // Data
    function (data) {
      if (data !== 0) {
        // Return error if name already in DB
        notifyRepeatedName(data);
      }
    }, // Success handler
    "text" // Response data type
  ).fail(function () {
    $clientNameInput = $("#client-name_zh");

    console.error(
      "An error occurred sending ajax request to " +
        $clientNameInput.attr("data-url") +
        " with parameters name: " +
        $clientNameInput.val()
    );
  });
});

/**
 * Informs the user that the Chinese name they have choosen for the client
 * already exists on the database.
 * @returns
 */
function notifyRepeatedName(data) {
  const $clientNameInput = $("div.field-client-name_zh input#client-name_zh");

  $("div.field-client-name_zh")
    .removeClass("has-success")
    .addClass("has-warning");
  $clientNameInput.after(data);

  // Attatch a focus in handler that deletes the warning
  $clientNameInput.focusin(function () {
    $("#duplicate-name-warning").remove();
  });
}

/**
 * Display the "family_role_other" field conditionally if role is "other"
 */
$("select#family-role-dropdown").on("change", function () {
  if (+this.value === 8) {
    $("div#family-role-other-container").slideDown();
  } else {
    $("div#family-role-other-container").slideUp();
  }
});

/* ***************************************************************************
 * 								PROGRAM
 *************************************************************************** */

/**
 * Calculate the overall financial data for a program.
 */
function calculateOverallFinancial() {
  const formatter = new Intl.NumberFormat("zh-CN", {
      style: "currency",
      currency: "CNY",
    }),
    $displayDue = $("#program-view-overview-due"),
    $displayPaid = $("#program-view-overview-paid"),
    $displayBalance = $("#program-view-overview-balance");

  let due = 0,
    paid = 0,
    balance = 0;

  // Add up all the values in the data-attrs
  $(".due-cell").each((index, value) => {
    due += +$(value).attr("data-due");
  });

  $(".paid-cell").each((index, value) => {
    paid += +$(value).attr("data-paid");
  });

  $(".balance-cell").each((index, value) => {
    balance += +$(value).attr("data-balance");
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
  const $balanceDisplay = $("td#program-family-overview-balance"),
    cost = Math.abs($("input#programfamily-cost").val()) || 0,
    discount = Math.abs($("input#programfamily-discount").val()) || 0,
    totalCost = cost - discount,
    balance = $balanceDisplay.attr("data-total-paid") - totalCost;

  // Update the UI and input values on the form
  $("input#programfamily-final_cost").val(totalCost);
  $("td#program-family-overview-final-cost").html(formatAsCurrency(totalCost));

  $balanceDisplay.html(formatAsCurrency(balance));

  if (balance < 0) {
    $balanceDisplay.addClass("financial-negative-balance");
  } else {
    $balanceDisplay.removeClass("financial-negative-balance");
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
  const formatter = new Intl.NumberFormat("zh-CN", {
    style: "currency",
    currency: "CNY",
    minimumFractionDigits: 2,
    // the default value for minimumFractionDigits depends on the currency  and is usually already 2
  });

  return formatter.format(amount);
}

/* ***************************************************************************
 * 					     	PROGRAM PRICE
 *************************************************************************** */

/**
 * Fetch all the Prices for a given program. *
 * @param id
 */
function fetchProgramPrices(id) {
  const url = Mh.globalData.apiurl + "program-prices?program-id=" + id;
  const $container = $("#program-prices-container");
  $container.html(Mh.globalData.spinner50);
  $.ajax({
    url,
    method: "GET",
    headers: Mh.globalData.requestHeaders,
    success: (res) => {
      // If successful will get an array of prices
      let html = "";
      res.forEach((p) => {
        html += generateProgramPriceHtmlTag(p);
      });
      $container.html(html);
    },
    error: (err) => {
      console.error(err);
      $container.html('<div class="alert alert-danger">下载价格时出错</div>');
    },
  });
}

/**
 * Generate the html tag for a program price. *
 * @param p
 * @returns {string}
 */
function generateProgramPriceHtmlTag(p) {
  let html =
    '<div class="program-price-container" id="program-price-' +
    p.id +
    '"' +
    ' data-program-price-id="' +
    p.id +
    '" data-program-id="' +
    p.program_id +
    '">';

  // Program price details
  html += '<div class="program-price-details">';
  html += p.adults + "大 " + p.kids + "小 ";
  html += parseInt(p.membership_type, 10) === 1 ? "会员 " : "非会员 ";
  html += p.price + "元";
  html += "</div>";

  // Update button
  html +=
    '<button class="btn btn-xs btn-primary update-program-price-button" ' +
    'onclick="showProgramPriceUpdateForm(' +
    p.id +
    "," +
    p.adults +
    "," +
    p.kids +
    "," +
    p.membership_type +
    "," +
    p.price +
    ')" type="button">更新</button>';

  // Delete button
  html +=
    '<button class="btn btn-xs btn-danger delete-program-price-button" ' +
    'onclick="deleteProgramPrice(' +
    p.id +
    ')" type="button">删除</button>';

  html += "</div>";

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
  const modal = $("#appModal");
  const modalContent = $("#modalContent");
  const spinner = $("<div>", {
    id: "program-price-form-loading-container",
    html: Mh.globalData.spinner50,
  });
  modalContent.html(spinner);
  $("#modalHeader").html("更新程序价格");
  modal.modal("show");
  // Get the update url from the container and replace the id
  const url = $("#program-prices-container")
    .attr("data-update-url")
    .replace(/0/gi, id);
  $.get(url, (res) => {
    $("#modalContent").append(res);
    $("#program-price-form-loading-container").hide();
  });
}

/**
 * Load and display the program-price create form.
 */
function showProgramPriceCreateForm(pid) {
  if (Mh.debug) {
    console.debug(`Fetching create form for program ${pid}`);
  }
  const modal = $("#appModal");
  const modalContent = $("#modalContent");
  const spinner = $("<div>", {
    id: "program-price-form-loading-container",
    html: Mh.globalData.spinner50,
  });
  modalContent.html(spinner);
  $("#modalHeader").html("创建新的计划价格");
  modal.modal("show");
  const url =
    $("#program-prices-container").attr("data-create-url") + "?pid=" + pid;
  $.get(url, (res) => {
    modalContent.append(res);
    $("#program-price-form-loading-container").hide();
  });
}

/**
 * Submit the information on the program-price form, it could be an update or
 * a new creation.
 *
 * @param id
 */
function submitProgramPriceForm() {
  let loadingContainer = $("#program-price-form-loading-container");
  let formContainer = $("#program-price-form-container");

  // Display loading gif
  formContainer.hide();
  loadingContainer.show();

  let data = {};
  $("form#program-price-form")
    .serializeArray()
    .map((e) => {
      if (e.name !== "_csrf") {
        data[e.name] = e.value;
      }
    });

  let method = "POST";
  let url = Mh.globalData.apiurl + "program-prices";
  if (data.id) {
    method = "PUT";
    url += "/" + data.id;
  }

  let authHeader = "Bearer " + userAccessToken;

  $.ajax({
    url: url,
    method: method,
    data: data,
    success: (res) => {
      // Hide the modal
      $("#appModal").modal("hide");

      // Check if we have an update or create
      if (data.id) {
        let tag = "#program-price-" + data.id;
        $(tag).replaceWith(generateProgramPriceHtmlTag(res));
      } else {
        $("#program-prices-container").append(generateProgramPriceHtmlTag(res));
      }

      // TODO check for errors like 422P
    },
    error: (res) => {
      console.warn("Error sending program-price request");
      console.warn(res);
      // TODO display error and react
    },
    beforeSend: (xhr) => {
      xhr.setRequestHeader("Authorization", authHeader);
    },
  });
}

/**
 * Delete a ProgramPrice instance and refresh the UI.
 *
 * @param id
 */
function deleteProgramPrice(id) {
  // Show a confirmation prompt
  const confirmed = confirm(
    "Are you sure you want to delete this item?" + " " + id
  );

  if (confirmed) {
    const $container = $("#program-price-" + id),
      $loadingOverlay = $("<div>", {
        html: loadingOverlaySmall,
      });

    // Animate displaying the loading overlay
    $loadingOverlay.hide();
    $container.append($loadingOverlay);
    $loadingOverlay.fadeIn("fast");

    $.ajax({
      url: Mh.globalData.apiurl + "program-prices/" + id,
      type: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Authorization: "Bearer " + userAccessToken,
      },
    })
      .done((res, status, xhr) => {
        // Success status for update is 200
        if (xhr.status === 204) {
          // Success deleting, remove the item
          $container.fadeOut("fast", () => {
            $container.remove();
          });
        } else {
          alert("There was an error deleting program price " + id);
        }
      })
      .fail((xhr) => {
        alert("There was an error deleting program price " + id);
      });
  }
}

/* ***************************************************************************
 * 								WEAPP
 *************************************************************************** */

/**
 * Fetch all the ProgramGroup images given it's ID and
 * display them in the application modal to let the user
 * select one as the ProgramGroup Wechat mini-program cover image
 */
$("button#update-weapp-cover-image").click(function () {
  const $modal = $("#cover-image-selection-modal");
  const pgId = $(this).attr("data-pg-id");
  const imgUrl = `${Mh.globalData.imgurl}pg/${pgId}/`;
  const url = Mh.globalData.apiurl + "images?program-group-id=" + pgId;
  let html = '<div id="pg-select-container">';

  $modal.modal();
  $modal.modal("show");

  $.ajax({
    url,
    method: "GET",
    headers: Mh.globalData.requestHeaders,
  })
    .done((res) => {
      res.forEach((image) => {
        html +=
          `<img class="pg-images-preview" alt="${image.name}" ` +
          `data-name="${image.name}" src="${imgUrl}${image.name}">`;
      });

      html += "</div>";
      // Update the modal body with the images
      $modal.find(".modal-body").html(html);

      // Add a click handler to the images
      $(".pg-images-preview").click(function () {
        const imgName = $(this).attr("data-name");
        $modal.modal("hide");
        // Update the values on the form
        $("input#programgroup-weapp_cover_image").val(imgName);
        $("img#pg-weapp-cover-image").attr("src", imgUrl + imgName);
      });
    })
    .fail((xhr) => {
      console.error(`Request to ${url} failed with status ${xhr.status}`, xhr);
    });
});

$("#download-images-button").click(function () {
  const $this = $(this);
  $this.html(Mh.globalData.spinner20);
  $this.removeClass("btn-success").addClass("btn-primary");
  const pgId = $this.attr("data-pg-id");
  const url = Mh.globalData.apiurl + "program-group-image-downloads/" + pgId;
  $.ajax({
    url,
    method: "PATCH",
    headers: Mh.globalData.requestHeaders,
  })
    .done((res, status, xhr) => {
      $this.html("完成了");
      $this.removeClass("btn-primary").addClass("btn-success");
      location.reload();
    })
    .fail((xhr) => {
      $this.html("下载错误");
      $this.removeClass("btn-primary").addClass("btn-danger");
    });
});

/**
 * Fetch
 * @param id
 */
function getProgramGroupImagesPreview(id) {
  let url = Mh.globalData.apiurl + "bu?program-group-id=" + id;
  let fu = $("#imageuploadform-file-fileupload");

  $.getJSON(url, (res) => {
    // TODO check for errors

    fu.fileupload("option", "done").call(fu, $.Event("done"), {
      result: { files: res },
    });
    displayProgramGroupImageTags();
  });
}

/**
 * Complete the initialization of the Blueimp file upload widget by displaying the
 * images on the first cell of their row.
 */
function displayProgramGroupImageTags() {
  let rows = $("#imageuploadform-file-fileupload .files tr");

  rows.each(function (index) {
    let cells = $(this).children();
    let firstCell = cells.eq(0);
    let link = cells.eq(1).find("a");
    let url = link.attr("href");
    let name = link.attr("title");
    let html =
      '<span class="preview"><a href="' +
      url +
      '" title="' +
      name +
      "" +
      '" download="' +
      name +
      '"><img src="' +
      url +
      '" alt="' +
      name +
      '"></a></span>';
    firstCell.html(html);
  });
}
