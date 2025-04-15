<?php
require_once 'helpers.php';
require_once 'controllers/DBController.php';
require_once 'controllers/LotController.php';
require_once 'controllers/CategoryController.php';

// Connect to the database
$db = new DBController();

$con = $db->connect();

// Get list of Lots
$lot = new LotController();

['data' => $lots, 'error' => $error] = $lot->getList($con);

if (isset($error['message'])) {
  print($error['message']);

  die();
}

$pageContent = includeTemplate('pages/index.php', ['lots' => $lots]);

$layoutData = [
  'title' => 'Главная',
  'content' => $pageContent,
  'dbConnection' => $con,
  'filePath' => __FILE__
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
