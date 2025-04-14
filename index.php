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

['data' => $data_lots, 'error' => $error] = $lot->getList($con);

if (is_array($data_lots)) {
  $lots = $data_lots;
} else if (isset($error['message'])) {
  print($error['message']);

  die();
}

// Get list of Categories
$category = new CategoryController();

['data' => $data_categories, 'error' => $error] = $category->getList($con);

if (is_array($data_categories)) {
  $categories = $data_categories;
} else if (isset($error['message'])) {
  print($error['message']);

  die();
}

$page_content = include_template('main.php', ['categories' => $categories,  'lots' => $lots]);

$layout_content = include_template('layout.php', ['title' => 'Главная', 'content' => $page_content, 'categories' => $categories,]);

print($layout_content);
