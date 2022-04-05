<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Setting up the time zone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Host Name
$dbhost = 'localhost'; // if host is different, then put it.

// Database Name
$dbname = 'admin_ipel';

// Database Username
$dbuser = 'admin_ipel';

// Database Password
$dbpass = 'Main**2014_';

// Defining base url
define("BASE_URL", "https://ipel.co.tz/");

// Getting Admin url
define("ADMIN_URL", BASE_URL . "admin" . "/");

try {
	$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $exception ) {
	echo "Connection error :" . $exception->getMessage();
}