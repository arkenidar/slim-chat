<?php
// Routes

$app->get('/', function ($req, $res, $args) {
    // redirect from index to chat_client.html
    return $res->withStatus(301)->withHeader('Location', '/chat_client.html');
});

$app->get('/chat/setup', function ($request, $response, $args) {
    
    $pdo = new PDO("sqlite:db.sqlite", "", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );

    $sql = "CREATE TABLE IF NOT EXISTS chat_messages (
      id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
      message_text TEXT NOT NULL,
      sender TEXT NOT NULL,
      creation_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL )";
    $pdo->query($sql); // init db tables
    
    echo "chat_messages table is setup";
});

$app->get('/chat/list', function ($request, $response, $args) use ($app) {

    $pdo = new PDO("sqlite:db.sqlite", "", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );
    
    $query = "SELECT * FROM chat_messages ORDER BY creation_timestamp DESC LIMIT 15";
    $query = "SELECT * FROM ($query) AS res ORDER BY creation_timestamp ASC";
    $messages = $pdo->query($query); // list messages

    $output = '';

    foreach($messages as $message){
        $sender = htmlspecialchars($message["sender"]);
        $text = htmlspecialchars($message["message_text"]);
        $image = substr($text, 0, 4)==="http"; // simple image handling stub
        $output .= $sender.": ".($image?"<img src=".$text.">":$text)."<br>";
    }
    
    echo $output;
});

$app->post('/chat/send', function ($request, $response, $args) {
    $pdo = new PDO("sqlite:db.sqlite", "", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );
    
    $postVars = $request->getParsedBody();
    
    $text = $pdo->quote($postVars["message_text"]);
    $sender = $pdo->quote($postVars["sender"]);    
    $sql = "INSERT INTO chat_messages (message_text, sender) VALUES ($text, $sender)";

    $pdo->query($sql); // insert new message
});