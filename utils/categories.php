<?php
require_once 'controllers/CategoryController.php';

// Get list of Categories
$category = new CategoryController($con);

['data' => $categories, 'error' => $errorCategories] = $category->getList();

if (is_array($categories)) {
  $nav = includeTemplate('components/nav.php', ['categories' => $categories]);
} else if (isset($error['message'])) {
  $nav = $error['message'];
}
