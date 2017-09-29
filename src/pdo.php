<?php

// supported DB types: 'sqlite', 'postgres', 'mysql'
define('pdo_db_type', 'mysql');
require 'conf.php';

function pdo(){

	switch(pdo_db_type){

		case 'sqlite':
			$db_url = 'sqlite:../db.sqlite';
			$pdo = new PDO($db_url, "", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );
			break;

		case 'postgres':
			$db_url = 'pgsql:host=localhost; dbname=messaging';
			$pdo = new PDO($db_url, 'postgres', postgres_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );
			break;

		case 'mysql':
			$db_url = 'mysql:host=localhost; dbname=messaging';
			$pdo = new PDO($db_url, 'root', mysql_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );
			break;
	}

	return $pdo;
}

function pdo_setup_db_sql(){
	$postgres_or_mysql = 'CREATE TABLE IF NOT EXISTS chat_messages (
      id SERIAL PRIMARY KEY NOT NULL,
      message_text TEXT NOT NULL,
      sender TEXT NOT NULL,
      creation_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL )';
	$setup_db_sql = [
	'sqlite' => 'CREATE TABLE IF NOT EXISTS chat_messages (
      id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
      message_text TEXT NOT NULL,
      sender TEXT NOT NULL,
      creation_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL )',
	'postgres' => $postgres_or_mysql,
    'mysql' => $postgres_or_mysql,
    ];
	$sql = $setup_db_sql[pdo_db_type];
	return $sql;
}

function pdo_execute($sql, $params = []) {
	$pdo = pdo();
	$stat = $pdo->prepare($sql);
	assert($stat);
	$res = $stat->execute($params);
	assert($res);
	return $stat;
}
