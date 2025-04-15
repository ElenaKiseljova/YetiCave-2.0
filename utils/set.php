<?php
require_once 'controllers/DBController.php';

// Connect to the database
$db = new DBController();

$con = $db->connect();
