let $loading20;
/**
 * Attach all the event handlers for Program Groups QA
 */
function attachHandlers () {

    $('textarea.qa-answer-textarea').change(function () {
        updateAnswer($(this));
    });

    // Create a jquery object with a loader image
    $loading20 = $(loading20);
}

/**
 * Send a request to the API to update the QA's answer.
 *
 * @param $textarea
 */
function updateAnswer($textarea) {

    const qaId = $textarea.attr('data-qa-id'),
        value = $textarea.val(),
        url = apiurl + 'qas/' + qaId,
        $container = $textarea.closest('.program-group-qa-container');

    $container.append($loading20);

    $.ajax({
        url: url,
        data: JSON.stringify({'answer': value}),
        type: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + userAccessToken
        }
    }).done((res, status, xhr) => {

        // Success status for update is 200
        if (xhr.status === 200) {

            $container.removeClass('qa-unanswered');

        } else {

            console.log('Some error');
            $container.addClass('qa-unanswered');
            // todo check and inform of 422 Validation errors

        }

    }).fail(xhr => {

        console.error(xhr); // todo
        $container.addClass('qa-unanswered');

    }).always(() => {

        // Remove the loading div
        $loading20.remove();

    });

}