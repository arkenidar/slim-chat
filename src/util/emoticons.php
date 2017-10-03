<?php

// textual emoticon to HTML emoticon
function textual_emoticon_to_html_emoticon($textual_emoticon_type){
    // example HTML emoticon : '<img src="img/ico/mail.png" class="emoticon" alt="mail">'
    $mapping = [
        'mail' => 'mail.png',
        'heart' => 'heart.gif',
    ];
    if(!array_key_exists($textual_emoticon_type, $mapping)) return null;
    $src = $mapping[$textual_emoticon_type];
    $html = '<img src="img/ico/'.$src.'" class="emoticon" alt="'.$textual_emoticon_type.'">';
    return $html;
}

// echo textual_emoticon_to_html_emoticon('mail');

// parse all textual emoticons expressions found in message to send
function parse_emoticons_expressions($string){
    $regex = '/\(\w+\)/i';
    $success = preg_match_all($regex, $string, $matches);
    $processed = [];

    $matches = $matches[0];

    for($i = 0; $i < count($matches); $i++) {
        $match = $matches[$i];
        $textual_emoticon_type = substr($match, 1, -1);
        $html_emoticon = textual_emoticon_to_html_emoticon($textual_emoticon_type);
        if($html_emoticon == null) continue;
        $processed[$match] = $html_emoticon;
    }

    foreach ($processed as $original => $value) {
        $string = str_replace($original, $value, $string);
    }
    return $string;
}

// echo parse_emoticons_expressions('(mail)(heart)(123)');

// $text = parse_emoticons_expressions($text);
