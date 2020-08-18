/**
 * Attach all the event handlers for Program Groups QA
 */
function attachHandlers () {
  const $answerBoxes = $('textarea.qa-answer-textarea');
  $answerBoxes.each(function () {
    const $this = $(this);
    $this.data('oldVal', $this.val());
    $this.data('modified', false);
    $this.on('input keyup change', function () {
      handleQaAnswer($this);
    });
  });
  $('.save-qa-answer-button').click(function () {
    const $this = $(this);
    $this.html('下载中...');
    const id = $this.attr('data-id');
    const $textarea = $(`textarea#qa-answer-${id}`);
    updateAnswer($textarea);
  });
  $('.delete-qa-answer-button').click(function () {
    deleteQa($(this));
  });
}

/**
 * Handle an update on the QA answer's textarea
 * @param $textarea
 */
function handleQaAnswer($textarea) {
  if (!$textarea.data('modified') && $textarea.data('oldVal') !== $textarea.val()) {
    $textarea.data('modified', true);
    displaySaveButton($textarea);
  }
}

/**
 * Display the save button to allow users to save changes to the QA's answer
 * @param $textarea
 */
function displaySaveButton($textarea) {
  const id = $textarea.attr('data-qa-id');
  const $button = $(`#save-qa-answer-button-${id}`);
  $button.fadeIn();
}

/**
 * Send a request to the API to update the QA's answer.
 *
 * @param $textarea
 */
function updateAnswer($textarea) {
  const qaId = $textarea.attr('data-qa-id');
  const value = $textarea.val();
  const url = Mh.globalData.apiurl + 'qas/' + qaId;
  const $container = $textarea.closest('.program-group-qa-container');
  const $button = $(`#save-qa-answer-button-${qaId}`);
  $.ajax({
    url: url,
    data: JSON.stringify({'answer': value}),
    type: 'PATCH',
    headers: Mh.globalData.requestHeaders,
  }).done((res, status, xhr) => {
    // Success status for update is 200
    if (xhr.status === 200) {
      if (res.answer) {
        $container.removeClass('qa-unanswered');
      } else {
        $container.addClass('qa-unanswered');
      }
      $textarea.data('oldVal', res.answer);
      $textarea.data('modified', false);
      $button.fadeOut(400, function () {
        $button.html($button.attr('data-text'));
      });
    } else {
      console.error('Error pushing values to the server');
      $container.addClass('qa-unanswered');
    }
  }).fail(xhr => {
    console.error(xhr); // todo
    $container.addClass('qa-unanswered');
  });
}

/**
 * Delete a FAQ from the database
 * @param id
 */
function deleteQa($button) {
  $button.data('text', $button.html());
  $button.html('删除中...');
  const id = $button.attr('data-id');
  if (confirm('确认删除常见问题?')) {
    $.ajax({
      url: Mh.globalData.apiurl + 'qas/' + id,
      type: 'DELETE',
      headers: Mh.globalData.requestHeaders,
    }).done((res, status, xhr) => {
      // Success status for update is 200
      if (xhr.status === 204) {
        $(`#program-group-qa-container-${id}`).fadeOut();
        Mh.methods.flashSuccess('成功删除常见问题');
      } else {
        console.error('Unexpected http response code ' + xhr.status);
        Mh.methods.flashError('删除常见问题解答时出现问题，请稍后重试');
      }
      $button.html($button.data('text'));
    }).fail(xhr => {
      console.error(xhr);
      Mh.methods.flashError('删除常见问题解答时出现问题，请稍后重试');
      $button.html($button.data('text'));
    });
  }
}
