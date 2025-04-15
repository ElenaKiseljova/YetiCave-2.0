<?php
require_once 'helpers.php';
require_once 'set.php';
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

$pageContent = includeTemplate('pages/lot.php', ['title' => $lotTitle, 'lot' => $lotData]);

$layoutData = [
  'title' => $lotTitle,
  'content' => $pageContent,
  'dbConnection' => $con,
  'filePath' => __FILE__
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
