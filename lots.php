<?php
require_once 'utils/helpers.php';
require_once 'includes/set.php';
require_once 'includes/auth.php';
require_once 'includes/categories.php';
require_once 'includes/archive.php';
require_once 'controllers/CategoryController.php';

if (isset($categoryId)) {
  $catCon = new CategoryController($con);

  ['data' => $category, 'error' => $error] = $catCon->getById($categoryId);

  if (isset($error['message'])) {
    print($error['message']);
  }
}

$pageContentData = [
  'nav' => $nav,
  'title' => $categoryId ? 'Все лоты в категории' : 'Все лоты',
  'titleInQuote' => isset($category['title']) ? $category['title'] : $categoryId,
  'lots' => $lots ?? [],
  'pages' => $pages,
  'pagesCount' => $pagesCount,
  'curPage' => $curPage
];

$pageContent = includeTemplate('pages/archive.php', $pageContentData);

$layoutData = [
  'isFull' => true,
  'title' => $categoryId ? ('Все лоты в категории ' . isset($category['title']) ? $category['title'] : $categoryId) : 'Все лоты',
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
