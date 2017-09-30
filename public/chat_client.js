var base_dir = window.location.pathname.split('/').slice(0,-1).join('/');

// chat scrolling
function scrollHeight(){ return $(document).height()-$(window).height(); }
function isScrolledToBottom(){ return $('html')[0].scrollTop == scrollHeight(); }
function scrollToBottom(){ $('html')[0].scrollTop = scrollHeight(); }

// message listing
function listMessages(scrollFlag){ $('#message_log').load(base_dir+'/chat/list', function(){ if(scrollFlag) scrollToBottom(); } ) }
function periodicallyListMessagesCallback(){ var scrollFlag = isScrolledToBottom(); listMessages(scrollFlag); }

// allow message sending
function send_message(){

    var form_data_object = {
        sender: $('input#sender').val().trim(),
        text: $('div#message_text').html().trim(),
    };

    // no blank message fields allowed
    if(form_data_object.text=='' || form_data_object.text==''){
         alert('no blank message fields allowed!');
         return false;
    }

    // disable form on pre-submit
    $('#send_message_form *').prop('disabled', true);

    // send JSON with POST type HTTP request
    $.ajax({
        type: 'POST',
        url: base_dir+'/chat/send',
        dataType: 'text',
        data: JSON.stringify(form_data_object),
    }).done(function(){
        // empty the message field
        $('div#message_text').html('');
    })
    .fail(function(){ // error handling
        alert('Error! (when sending a message)');
    })
    .always(function(){
        // enable form on post-submit
        $('#send_message_form *').prop('disabled', false);
    });
}

// on ready
$(function(){

    // stay updated
    setInterval(periodicallyListMessagesCallback, 1000); // get messages periodically

    $('#send').click(send_message);
});

