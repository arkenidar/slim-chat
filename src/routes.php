<?php

// Routes (routes.php)

// - PDO
require 'pdo.php';

// phpinfo()
$app->get('/util/phpinfo', function ($request, $response, $args) {
    phpinfo();
});

// setup database tables
$app->get('/util/db_setup', function ($request, $response, $args) {
    pdo_execute(pdo_setup_db_sql());
    echo 'DB tables are now setup.';
});

// home page
$app->get('/', function ($request, $response, $args) {
    // redirect from index to chat_client.html
    return $response->withStatus(301)->withHeader('Location', 'chat_client.html');
});

require 'util/emoticons.php';

// list messages
$app->get('/chat/list', function ($request, $response, $args) use ($app) {
    // OUT: $messages
    $messages = pdo_execute('SELECT * FROM (SELECT * FROM chat_messages ORDER BY creation_timestamp DESC LIMIT 15) AS res ORDER BY creation_timestamp ASC');
    // - produce HTML output
    // IN: $messages OUT: $output
    $output = "<style> .line { white-space: pre-wrap; } </style>\n";
    foreach($messages as $message) {
        $sender = htmlspecialchars($message['sender']);
        $text = htmlspecialchars($message['message_text']);
        $text = parse_emoticons_expressions($text);
        $output .= "<div class=\"line\">$sender: $text</div>\n";
    }
    echo $output;
});

// insert new message
$app->post('/chat/send', function ($request, $response, $args) {
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
