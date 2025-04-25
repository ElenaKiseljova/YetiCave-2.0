<?php
require_once 'utils/helpers.php';
require_once 'includes/set.php';
require_once 'includes/auth.php';
require_once 'includes/categories.php';
require_once 'controllers/BetController.php';

if (!$isAuth) {
  header('Location: /login');

  die();
}

$betCon = new BetController($con);

['data' => $bets, 'error' => $betsError] = $betCon->getHistory($userId);

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
