<?php
require_once 'helpers.php';
require_once 'controllers/DBController.php';
require_once 'controllers/CategoryController.php';

// List of Categories (default)
$categories = [];

// Connect to the database
$db = new DBController();

$con = $db->connect();

// Get list of Categories
$category = new CategoryController();

['data' => $dataCategories, 'error' => $error] = $category->getList($con);

if (is_array($dataCategories)) {
  $categories = $dataCategories;
} else if (isset($error['message'])) {
  print($error['message']);

  die();
}

$nav = includeTemplate('components/nav.php', ['categories' => $categories]);

$pageContent = includeTemplate('pages/error.php', ['nav' => $nav]);

$layoutContent = includeTemplate('layout.php', ['title' => "Ошибка {$_SERVER['REDIRECT_STATUS']}", 'content' => $pageContent, 'nav' => $nav,]);

print($layoutContent);
