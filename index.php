<?php
require_once 'helpers.php';
require_once 'set.php';
require_once 'controllers/LotController.php';

// Get list of Lots
$lot = new LotController();

['data' => $lots, 'error' => $error] = $lot->getList($con);

if (!$lots) {
  if (isset($error['message'])) {
    print($error['message']);
  }

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
