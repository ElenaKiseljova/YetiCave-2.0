<?php
require_once 'utils/helpers.php';
require_once 'includes/set.php';
require_once 'includes/auth.php';
require_once 'includes/categories.php';
require_once 'includes/archive.php';

$pageContentData = [
  'nav' => $nav,
  'title' => 'Результаты поиска по запросу',
  'titleInQuote' => $search,
  'lots' => $lots ?? [],
  'pages' => $pages,
  'pagesCount' => $pagesCount,
  'curPage' => $curPage
];

$pageContent = includeTemplate('pages/archive.php', $pageContentData);

$layoutData = [
  'isFull' => true,
  'title' => 'Поиск лота',
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
