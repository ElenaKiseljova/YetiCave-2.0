<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/BetController.php';

if (!$isAuth) {
  header('Location: /login');

  die();
}

$betCon = new BetController();

['data' => $bets, 'error' => $betsError] = $betCon->getHistory($con, $userId);

if (isset($betsError['message'])) {
  print($betsError['message']);
}

$pageContentData = [
  'nav' => $nav,
  'bets' => $bets
];

$pageContent = includeTemplate('pages/my-bets.php', $pageContentData);

$layoutData = [
  'isFull' => true,
  'title' => 'Мои ставки',
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
