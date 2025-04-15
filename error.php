<?php
require_once 'helpers.php';
require_once 'controllers/DBController.php';
require_once 'controllers/CategoryController.php';

// Connect to the database
$db = new DBController();

$con = $db->connect();

$pageContent = includeTemplate('pages/error.php');

$layoutData = [
  'title' => "Ошибка {$_SERVER['REDIRECT_STATUS']}",
  'content' => $pageContent,
  'dbConnection' => $con,
  'filePath' => __FILE__
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
