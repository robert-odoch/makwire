/*** Sending a message. ***/

// Always scroll to the end of the messages.
$('.chat-content').scrollTo('100%', 1000);
$('.col-small').on('click', '.active-users a', function(event) {
    event.preventDefault();

    var url = $(this).attr('href');
    $('.col-small').load(url, function() {
        $('.col-small .chat-content').scrollTo('100%', 1000);
    });
});

$('.col-small').on('submit', 'form', function(event) {
    event.preventDefault();

    var $messageField = $(this).find(':input:text');
    var message = $messageField.val();
    if (message.length == 0) {  // Show an error message.
        $messageField.addClass('has-error');
        $messageField.parents('form').append("<span class='error'>Message can't be empty!</span>");
    }
    else {  // Send the message.
        var url = $(this).attr('action');
        var params = $(this).serialize();
        $.post(url, params, function(data) {
            // Insert the message immediately above the form.
            var html = $.parseHTML(data);
            $(html).insertBefore('.chat-content .new-message');

            // Scroll the form into view.
            $('.chat-content').scrollTo('100%', 1000);
        });
    }
});

/*** Returning back to active users. ***/
$('.col-small').on('click', '.back-btn', function(event) {
    event.preventDefault();

    var url = $(this).attr('href');
    $('.col-small').load(url);
});

/*** Viewing previous messages. ***/
function previousMessages(event) {
    event.preventDefault();

    var url = $(this).attr('href');
    $.get(url, function(data) {
        // Remove the link for viewing previous messages.
        $('.chat-content .previous').remove();

        // Insert the returned HTML at the beginning of the div.
        var html = $.parseHTML(data);
        $(html).prependTo('.chat-content');
    });
}

$('.chat-content').on('click', '.previous', previousMessages);
$('.col-small').on('click', '.previous', previousMessages);
