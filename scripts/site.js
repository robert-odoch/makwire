var submittingIndicator = "<img src='http://localhost/makwire/images/ajax-loader.gif' class='submitting'>";
var loadingIndicator    = "<div class='loading'>" +
                                "<div>" +
                                    "<img src='http://localhost/makwire/images/ajax-loader.gif'>" +
                                    "<span>&nbsp;Loading...</span>" +
                                "</div>" +
                            "</div>";

/*** Tooltips ***/
$('[data-toggle="tooltip"]').tooltip();

/*** Status updates ***/
// Showing forms.
$('#status-nav li a').click(function(event) {
    event.preventDefault();

    var $this = $(this);
    var $box = $this.parents('div.box');

    // Remove any alerts and replace form with loading indicator.
    var $form = $box.find('form');
    var formHeight = $form.css('height');

    // Add 10px for margin-top on labels.
    formHeight = parseInt(formHeight) + 10 + 'px';

    $box.find('.alert').remove();
    $form.replaceWith($(loadingIndicator));
    $box.find('.loading div').css({
        height: formHeight,
        paddingTop: '40px'
    });

    var url = $this.attr('href');
    $.get(url, function(data) {
        var html = $.parseHTML(data);

        // Move the active class to the li parent for this link.
        $box.find('.active').removeClass('active');
        $this.parents('li').addClass('active');

        // Remove loading indicator.
        $box.find('.loading').remove();

        // Show the new form.
        $(html).appendTo($box);
    });
});

// Posting an update.
$('#update-status').on('submit', 'form', function(event) {
    var $this = $(this);

    // For now we can't post photos using AJAX.
    if ($this.find('input:file').length > 0) {
        return;
    }

    event.preventDefault();

    // Show submitting indicator.
    $this.find('input:submit').after($(submittingIndicator));
    $this.find('img.submitting').css({marginLeft: '5px'});

    var formAction = $this.attr('action');
    $.post(formAction, $this.serialize(), function(data) {
        // Remove submitting indicator.
        $this.find('img.submitting').remove();

        // Process result.
        var result = $.parseJSON(data);
        var $input = $this.find('textarea, input').not(':submit');
        if (result.error) {
            if ($input.is(':file')) {  // Adding photo.
                var errorMarkup = "<div class='alert alert-danger' role='alert'>" +
                                        "<span class='fa fa-exclamation-circle'></span>" +
                                        result.error +
                                    "</div>";
                $(errorMarkup).insertBefore($this);
            }
            else {
                // Remove any previous error message (s).
                $this.find('span.error').remove();

                // Show the new error message.
                if ( ! $input.hasClass('has-error')) {
                    $input.addClass('has-error');
                }

                var $errorMarkup = $("<span class='error'>" + result.error + "</span>");
                $input.after($errorMarkup);
            }
        }
        else {
            // Remove any previous error message (s).
            $input.removeClass('has-error');
            $this.find('span.error').remove();

            // Clear the input field.
            if ($input.not(':file')) {
                $input.val('');
            }

            var item = $.parseHTML(result.item);
            $(item).insertAfter($this.parents('.box'));
        }
    });
});

/*** View more of a long post. ***/
$('.more.post').click(function(event) {
    event.preventDefault();

    var $this = $(this);
    var url = $this.attr('href');
    $.get(url, function(post) {
        $this.parents('p').text(post);
    });
});

/*** Liking an item. ***/
$('body').on('click', '.like', function(event) {
    event.preventDefault();

    var $this = $(this);
    var url = $this.attr('href');
    $.get(url, function(data) {
        var result = $.parseJSON(data);

        // Remove focus from this link.
        $this.trigger('blur');

        // Indicate that the item has been liked.
        $this.find('.fa').removeClass('fa-thumbs-o-up').addClass('fa-thumbs-up');

        // Update the likes link.
        if (parseInt(result.numLikes) > 0) {
            var $parent = $this.parents('.footer');

            // Only show middot if there are other links besides.
            if ($this.siblings('a').length > 0) {
                $parent.find('span.likes').removeClass('hidden');
            }

            var $likesLink = $parent.find('a.likes');
            if ($likesLink.hasClass('hidden')) {
                $likesLink.removeClass('hidden');
            }
            $likesLink.text(result.numLikes);

            // If we are on the page for likes, show the like.
            if (result.like) {
                var likeMarkup = $.parseHTML(result.like);
                var $likesDiv = $('div.likes');

                // Remove alert for no likes.
                $likesDiv.siblings('.alert').remove();

                // Show the like.
                if ($likesDiv.hasClass('hidden')) {
                    $likesDiv.removeClass('hidden');
                }
                $(likeMarkup).prependTo($likesDiv);
            }
        }
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

    // Remove current chat users and show loading indicator.
    $('.active-users').replaceWith($(loadingIndicator));

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

    // Show loading indicator.
    $(loadingIndicator).insertBefore('.chat-content .new-message');

    var url = $(this).attr('href');
    $.get(url, function(data) {
        var html = $.parseHTML(data);

        // Remove the loading indicator.
        $('.chat-content .loading').remove();

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

    // Remove the link for viewing previous messages and show loading indicator.
    $('.chat-content .previous').replaceWith($(loadingIndicator));

    var url = $(this).attr('href');
    $.get(url, function(data) {
        var html = $.parseHTML(data);

        // Remove the loading indicator.
        $('.chat-content .loading').remove();

        // Insert the returned HTML at the beginning of the div.
        $(html).prependTo('.chat-content');
    });
}

$('.chat-content').on('click', '.previous', previousMessages);
$('.col-small').on('click', '.previous', previousMessages);

/*** Returning back to active users. ***/
$('.col-small').on('click', '.back-btn', function(event) {
    event.preventDefault();

    // Remove current chat user and show loading indicator.
    var $colSmall = $('.col-small');

    $colSmall.empty();
    $(loadingIndicator).prependTo($colSmall);

    // Load active users.
    var url = $(this).attr('href');
    $colSmall.load(url);
});

/*** Loading more news feed and timeline items. ***/
function moreNewsFeedTimelineItems(event) {
    event.preventDefault();

    var $this = $(this);
    var $parent = $this.parents('div.more');

    // Show loading indicator.
    $this.replaceWith($(loadingIndicator));

    var url = $this.attr('href');
    $.get(url, function(data) {
        // Load and display more items.
        var html = $.parseHTML(data);
        $parent.replaceWith($(html));
    });
}

$('.more').on('click', '.timeline', moreNewsFeedTimelineItems);
$('.more').on('click', '.news-feed', moreNewsFeedTimelineItems);
