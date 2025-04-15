<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';

$pageContent = includeTemplate('pages/error.php', ['nav' => $nav]);

$layoutData = [
  'isFull' => true,
  'title' => "Ошибка {$_SERVER['REDIRECT_STATUS']}",
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
