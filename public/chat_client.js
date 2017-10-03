const base_dir = window.location.pathname.split('/').slice(0,-1).join('/');

// on ready
$(function(){

    // stay updated
    setInterval(periodicallyListMessagesCallback, 1000); // get messages periodically

    $('#send').click(send_message);

    $('#message_text').on('input', on_input);
    $('#edit_tools').click(()=>{
        $('#message_text').html($('#message_text').html()+'(smile)');
        $('#message_text').trigger('input');
    });

});

// chat scrolling
function scrollHeight(){ return $(document).height()-$(window).height(); }
function isScrolledToBottom(){ return $('html')[0].scrollTop == scrollHeight(); }
function scrollToBottom(){ $('html')[0].scrollTop = scrollHeight(); }

// message listing
function listMessages(scrollFlag){ $('#message_log').load(base_dir+'/chat/list', function(){ if(scrollFlag) scrollToBottom(); } ) }
function periodicallyListMessagesCallback(){ var scrollFlag = isScrolledToBottom(); listMessages(scrollFlag); }

function get_input(){
    replace_all_html_emoticons();
    return $('div#message_text')[0].innerText;
}

// allow message sending
function send_message(){

    // no blank message fields allowed
    var sender = $('input#sender').val().trim();
    if(sender==''){
         alert('fill the Sender field!');
         return false;
    }

    var input = get_input();
    if(input==''){
         alert('fill the Message field!');
         return false;
    }

    var form_data_object = {
        sender: sender,
        text: input,
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

// replace all HTML emoticons with textual emoticons before sending, to preserve them
function replace_all_html_emoticons(){
    // emoticon's img elements
    var imgs = $('#message_text img[class="emoticon"]');
    imgs.each(function () {
        var html_emoticon_element = $(this);
        replace_html_emoticon(html_emoticon_element);
    });

    // replace HTML emoticon with textual emoticon
    function replace_html_emoticon(html_emoticon_element){
        html_emoticon_element.replaceWith('('+html_emoticon_element.attr('alt')+')');
    }
}

// textual emoticon to HTML emoticon
function textual_emoticon_to_html_emoticon(textual_emoticon_text){
    // example HTML emoticon : '<img src="img/ico/mail.png" class="emoticon" alt="mail">'
    var mapping = {
        mail: 'mail.png',
        heart: 'heart.gif',
        smile: 'smile.png',
    };
    var textual_emoticon_type = textual_emoticon_text.slice(1,-1);
    var src = mapping[textual_emoticon_type];
    // for invalid mappings...
    if(typeof src == 'undefined') return textual_emoticon_text;
    var html = '<img src="img/ico/'+src+'" class="emoticon" alt="'+textual_emoticon_type+'">';
    return html;
}

// parse all textual emoticons expressions found in message to send
function parse_emoticons_expressions(string){
    return string.replace(/(\(\w+\))/gi, textual_emoticon_to_html_emoticon);
}

function escapeRegExp(str) {
  return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

function replace_all(string, replace_what, replace_with){
    return string.replace(
        new RegExp(escapeRegExp(replace_what), 'gi'),
        replace_with
    );
}

function replace_smileys(input){
    var mappings = [
        [':)', '(smile)'],
        ['<3', '(heart)'],
    ];
    for(mapping of mappings){
        input = replace_all(input, mapping[0], mapping[1]);
    }
    return input;
}

function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined"
            && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}

function on_input(inputEvent){
    var input = get_input();
    input = replace_smileys(input)
    var new_html = parse_emoticons_expressions(input);
    if(new_html!=null){
        $(this).html(new_html);
        placeCaretAtEnd($(this)[0]);
    }
}
