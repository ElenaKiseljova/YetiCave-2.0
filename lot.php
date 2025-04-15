<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/LotController.php';

$lotId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$lotId) {
  http_response_code(404);

  die();
}

// Get list of Lots
$lot = new LotController();

['data' => $lotData, 'error' => $error, 'success' => $success] = $lot->getItem($con, $lotId);

if (!$lotData) {
  http_response_code(404);

  die();
}

$lotTitle = isset($lotData['title']) ? htmlspecialchars($lotData['title']) : 'Лот';

$pageContent = includeTemplate('pages/lot.php', ['title' => $lotTitle, 'lot' => $lotData, 'nav' => $nav]);

$layoutData = [
  'isFull' => true,
  'title' => $lotTitle,
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
