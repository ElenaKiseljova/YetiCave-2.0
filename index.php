<?php
require_once 'helpers.php';

// List of Lots (default)
$lots = [];

// List of Categories (default)
$categories = [];

// Connect to the database
$con = db_connect();

// Get list of Lots
['data' => $data_lots, 'error' => $error] = get_lots($con);

if (is_array($data_lots)) {
  $lots = $data_lots;
} else if (isset($error['message'])) {
  print($error['message']);

  die();
}

// Get list of Categories
['data' => $data_categories, 'error' => $error] = get_categories($con);

if (is_array($data_categories)) {
  $categories = $data_categories;
} else if (isset($error['message'])) {
  print($error['message']);

  die();
}

$page_content = include_template('main.php', ['categories' => $categories,  'lots' => $lots]);

$layout_content = include_template('layout.php', ['title' => 'Главная', 'content' => $page_content, 'categories' => $categories,]);

print($layout_content);
