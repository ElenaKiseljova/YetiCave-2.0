<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/LotController.php';

// GET params
$data = [
  'search' => FILTER_UNSAFE_RAW,
  'page' => FILTER_UNSAFE_RAW,
  'per_page' => FILTER_UNSAFE_RAW,
];

$params = filter_input_array(INPUT_GET, $data);

// Get list of Lots
$lot = new LotController();

$search = $params['search'] ? trim(htmlspecialchars($params['search'])) : '';

// Get count of Lots
['data' => $lotsCount, 'error' => $errorLotsCount] = $lot->count($con, $search);

if ($errorLotsCount) {
  if (isset($errorLotsCount['message'])) {
    print($errorLotsCount['message']);
  }
}

$curPage = $params['page'] ? intval($params['page']) : 1;
$perPage = $params['per_page'] ? intval($params['per_page']) : 2;
$pagesCount = ceil($lotsCount / $perPage);
$offset = ($curPage - 1) * $perPage;
$pages = range(1, $pagesCount);

// Get list og Lots
['data' => $lots, 'error' => $error] = $lot->paginate($con, $perPage, $offset, $search);

if ($error) {
  if (isset($error['message'])) {
    print($error['message']);
  }
}

$pageContentData = [
  'nav' => $nav,
  'search' => $search,
  'lots' => $lots ?? [],
  'pages' => $pages,
  'pagesCount' => $pagesCount,
  'curPage' => $curPage
];

$pageContent = includeTemplate('pages/search.php', $pageContentData);

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
