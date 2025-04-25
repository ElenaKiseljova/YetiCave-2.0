<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';

define('TIMEZONE', 'Europe/Kyiv');

date_default_timezone_set(TIMEZONE);

// Connect to the database
$dbCon = new DBController();

$con = $dbCon->connect();
