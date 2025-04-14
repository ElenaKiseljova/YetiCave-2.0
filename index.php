<?php
require_once 'helpers.php';
require_once 'controllers/DBController.php';
require_once 'controllers/LotController.php';
require_once 'controllers/CategoryController.php';

// List of Lots (default)
$lots = [];

// List of Categories (default)
$categories = [];

// Connect to the database
$db = new DBController();

$con = $db->connect();

// Get list of Lots
$lot = new LotController();

['data' => $dataLots, 'error' => $error] = $lot->getList($con);

if (is_array($dataLots)) {
  $lots = $dataLots;
} else if (isset($error['message'])) {
  print($error['message']);

  die();
}

// Get list of Categories
$category = new CategoryController();

['data' => $dataCategories, 'error' => $error] = $category->getList($con);

if (is_array($dataCategories)) {
  $categories = $dataCategories;
} else if (isset($error['message'])) {
  print($error['message']);

  die();
}

$pageContent = includeTemplate('main.php', ['categories' => $categories,  'lots' => $lots]);

$layoutContent = includeTemplate('layout.php', ['title' => 'Главная', 'content' => $pageContent, 'categories' => $categories,]);

print($layoutContent);
