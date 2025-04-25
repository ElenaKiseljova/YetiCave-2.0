<?php
require_once 'utils/helpers.php';
require_once 'includes/set.php';
require_once 'includes/auth.php';
require_once 'includes/categories.php';
require_once 'controllers/LotController.php';

// Get list of Lots
$lot = new LotController($con);

['data' => $lots, 'error' => $error] = $lot->getList();

if ($error) {
  if (isset($error['message'])) {
    print($error['message']);
  }

  die();
}

$pageContent = includeTemplate('pages/index.php', ['categories' => $categories, 'lots' => $lots]);

$layoutData = [
  'title' => 'Главная',
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
