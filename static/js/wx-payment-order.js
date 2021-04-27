$('#check-order-status-button').click(function () {
  const $this = $(this);
  const orderId = $this.attr('data-order-id');
  console.log('Clicked check order ' + orderId);
  checkOrderStatus(orderId);
});

/**
 * Send a request to the private API to check the current status of this order.
 * @param id The order id on the local system. The API will use it to obtain
 * and send the WeChat transaction_id parameter.
 */
function checkOrderStatus(id) {
  const $modal = $('#appModal');
  const $modalContent = $('#modalContent');
  const $spinner = $('<div>', {
    id: 'check-order-status-loading-container',
    html: Mh.globalData.spinner50
  });
  $modalContent.html($spinner);
  $('#modalHeader').html('从微信接口请求数据');
  $modal.modal('show');
  $.ajax({
    url: Mh.globalData.apiurl + 'wx-payment-check-orders/' + id,
    data: {id},
    method: "PUT",
    headers: Mh.globalData.requestHeaders,
    success: res => {
      // If successful will get a string
      console.log(res);
      $modalContent.html(`Success, got info`);
    },
    error: err => {
      console.error(err);
      $modalContent.html('Error');
    }
  });
}
