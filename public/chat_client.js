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

    var sender = $('input#sender').val().trim();
    // no blank message fields allowed
    if(sender==''){
         alert('no blank sender field allowed!');
         return false;
    }
    if($('div#message_text').html()==''){
         alert('no blank message field allowed!');
         return false;
    }

    replace_all_html_emoticons();

    var form_data_object = {
        sender: sender,
        text: $('div#message_text')[0].innerText,
    };

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

// replace HTML emoticons with textual emoticons before sending, to preserve them
function replace_all_html_emoticons(){
    // emoticon's img elements
    var imgs = $('#message_text img[class="emoticon"]');
    imgs.each(function () {
        var html_emoticon_element = $(this);
        replace_html_emoticon(html_emoticon_element);
    });
}

// replace HTML emoticon with textual emoticon
function replace_html_emoticon(html_emoticon_element){
    html_emoticon_element.replaceWith('('+html_emoticon_element.attr('alt')+')');
}

// parse all textual emoticons expressions found in message to send
function parse_emoticons_expressions(str){
    var regex = /\(\w+\)/gi;
    var matches = str.match(regex);
    if(matches == null)
        return null;
    var processed = [];

    for(var i in matches){
        var match = matches[i];
        var textual_emoticon_type = match.slice(1,-1);
        var html_emoticon = textual_emoticon_to_html_emoticon(textual_emoticon_type);
        if(html_emoticon == null) continue;
        processed[match] = html_emoticon;
    }

    if(Object.keys(processed).length == 0)
        return null;

    for(var original in processed){
        str = str.replace(original, processed[original]);
    }
    return str;
}

// textual emoticon to HTML emoticon
function textual_emoticon_to_html_emoticon(textual_emoticon_type){
    // example HTML emoticon : '<img src="img/ico/mail.png" class="emoticon" alt="mail">'
    var mapping = {
        mail: 'mail.png',
        heart: 'heart.gif',
    };

    var src = mapping[textual_emoticon_type];
    // for invalid mappings return null
    if(typeof src == 'undefined') return null;
    var html = '<img src="img/ico/'+src+'" class="emoticon" alt="'+textual_emoticon_type+'">';
    return html;
}

// on ready
$(function(){

    // stay updated
    setInterval(periodicallyListMessagesCallback, 1000); // get messages periodically

    $('#send').click(send_message);

    $("#message_text").on('input', function(e){
        var new_html = parse_emoticons_expressions( $("#message_text").html() );
        if(new_html!=null)
            $("#message_text").html(new_html);
    });

});
