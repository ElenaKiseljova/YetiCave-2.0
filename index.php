<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/LotController.php';

// Get list of Lots
$lot = new LotController();

['data' => $lots, 'error' => $error] = $lot->getList($con);

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
