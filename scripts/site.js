$('.col-small').on('click', '.active-users a', function(event) {
    event.preventDefault();

    var href = $(this).attr('href');
    var userID = href.substring(href.lastIndexOf('/')+1);
    var url = 'http://localhost/makwire/user/get-chat-user/' + userID;
    $('.col-small').load(url);

    // TODO: Scoll to the last message.
});

$('.col-small').on('click', '.back-btn', function(event) {
    event.preventDefault();

    var url = 'http://localhost/makwire/user/get_chat_users';
    $('.col-small').load(url);
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
        var action = $(this).attr('action');
        var data = $(this).serialize();
        $.post(action, data);

        // Re-display messages.
        var userID = action.substring(action.lastIndexOf('/')+1);
        var url = 'http://localhost/makwire/user/get-chat-user/' + userID;
        $('.col-small').load(url);
    }
});
