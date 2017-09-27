var loading = "<div class='loading'><img src='http://localhost/makwire/images/ajax-loader.gif'></div>";
/*** Tooltips ***/
$('[data-toggle="tooltip"]').tooltip();

/*** Status updates ***/
$('#status-nav li a').click(function(event) {
    event.preventDefault();

    var $this = $(this);
    var url = $this.attr('href');
    $.get(url, function(data) {
        var html = $.parseHTML(data);
        var $box = $this.parents('div.box');

        // Move the active class to the li parent for this link.
        $box.find('.active').removeClass('active');
        $this.parents('li').addClass('active');

        // Remove the old form.
        $box.find('form').remove();

        // Show the new form.
        $(html).appendTo($box);
    });
});

/*** Sending a message. ***/

// Always scroll to the end of the messages.
$('.chat-content').scrollTo('100%', 1000);
$('.new-message').find(':input:text').focus();

// Chat sidebar.
$('body').on('click', 'a.send-message', function(event) {
    if ($(window).width() < 972) {
        // Chat sidebar hidden...
        return;
    }

    event.preventDefault();

    var url = $(this).attr('href');
    $('.col-small').load(url, function() {
        $('.col-small .chat-content').scrollTo('100%', 1000);
        $('.new-message').find(':input:text').focus();

        // Enable tooltip for refresh-chat.
        $('[data-toggle="tooltip"]').tooltip();
    });
});

$('body').on('submit', 'form.send-message', function(event) {
    event.preventDefault();

    var $this = $(this);
    var $messageField = $this.find(':input:text');
    var message = $messageField.val();
    if (message.length == 0) {  // Show an error message.
        $messageField.addClass('has-error');

        // Remove any previous error messages if any.
        $this.children('span.error').remove();

        // Show the new error message.
        $this.append("<span class='error'>Message can't be empty!</span>");

        // Scroll the form into view.
        $('.chat-content').scrollTo('100%', 1000);
    }
    else {  // Send the message.
        var url = $(this).attr('action');
        var params = $(this).serialize();
        $.post(url, params, function(data) {
            // Clear the input field.
            $messageField.val('');

            // Insert the message immediately above the form.
            var html = $.parseHTML(data);
            $(html).insertBefore('.chat-content .new-message');

            // Scroll the form into view.
            $('.chat-content').scrollTo('100%', 1000);
        });
    }

    // Focus the message input field.
    $messageField.focus();
});

/*** Refreshing messages. ***/
$('body').on('click', '.refresh-chat', function(event) {
    event.preventDefault();

    // Show loading feedback.
    $(loading).insertBefore('.chat-content .new-message');

    var url = $(this).attr('href');
    $.get(url, function(data) {
        // Remove the loading feedback.
        $('.chat-content .loading').remove();

        var html = $.parseHTML(data);
        if (html !== null) {
            // Insert the message immediately above the form.
            $(html).insertBefore('.chat-content .new-message');

            // Scroll the form into view.
            $('.chat-content').scrollTo('100%', 1000);
        }

        // Focus the message input field.
        $('.new-message').find(':input:text').focus();
    });
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

/*** Returning back to active users. ***/
$('.col-small').on('click', '.back-btn', function(event) {
    event.preventDefault();

    var url = $(this).attr('href');
    $('.col-small').load(url);
});
