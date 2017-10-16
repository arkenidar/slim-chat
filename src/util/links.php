<?php

function create_html_links($text){
    $html = preg_replace_callback(
        '/((https?|ftps?)\:\/\/\S*)/',
        create_function(
            '$url',
            'return \'<a href="\'.htmlspecialchars($url[1]).\'"'.
            ' target="_blank">\'.htmlspecialchars($url[1]).\'</a>\';' 
            ),
        $text);
    return $html;
}
