<?php
/*
Copyright 2017 Dario Cangialosi

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// Routes (routes.php)
use Slim\Http\Request;
use Slim\Http\Response;

// - PDO
require 'util/pdo.php';

// phpinfo()
$app->get('/util/phpinfo', function () {
    phpinfo();
});

// setup database tables
$app->get('/util/db_setup', function () {
    pdo_execute(pdo_setup_db_sql());
    echo 'DB tables are now setup.';
});

// home page
$app->get('/', function (Request $request, Response $response) {
    // redirect from index to chat_client.html
    return $response->withRedirect('chat_client.html');
});

require 'util/emoticons.php';

// list messages
$app->get('/chat_list', function () {
    // OUT: $messages
    $messages = pdo_execute('SELECT * FROM (SELECT * FROM chat_messages ORDER BY creation_timestamp DESC LIMIT 15) AS res ORDER BY creation_timestamp ASC');
    // - produce HTML output
    // IN: $messages OUT: $output
    $output = '<style> .line { white-space: pre-wrap; } </style>'."\n"
    .'<link rel="stylesheet" type="text/css" href="chat_client.css">';
    foreach($messages as $message) {
        $sender = htmlspecialchars($message['sender']);
        $text = htmlspecialchars($message['message_text']);
        $text = parse_emoticons_expressions($text);
        $output .= "<div class=\"line\"><u>$sender</u>: $text</div>\n";
    }
    echo $output;
});

// insert new message
$app->post('/chat_send', function (Request $request) {
    $unparsedBodyJSON = $request->getBody();
    // IN: $request OUT: $message
    //$unparsedBodyJSON = $request->getBody();
    // parse the JSON into an associative array
    $message = json_decode($unparsedBodyJSON, true);
    // $message has value ['text' => ..., 'sender' => ...];    
    // - SQL chat send
    // IN: $message
    pdo_execute('INSERT INTO chat_messages (message_text, sender) VALUES (:text, :sender)', $message);
});
