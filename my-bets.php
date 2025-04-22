<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/DBController.php';
require_once 'controllers/BetController.php';

if (!$isAuth) {
  header('Location: /login');

  die();
}

$pageContentData = [
  'nav' => $nav
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
