<?php
require_once 'controllers/LotController.php';

// GET params
$data = [
  'search' => FILTER_UNSAFE_RAW,
  'category_id' => FILTER_UNSAFE_RAW,
  'page' => FILTER_UNSAFE_RAW,
  'per_page' => FILTER_UNSAFE_RAW,
];

$params = filter_input_array(INPUT_GET, $data);

// Get list of Lots
$lot = new LotController();

// Search page
$search = isset($params['search']) ? htmlspecialchars(trim($params['search'])) : null;

// Category page
$categoryId = isset($params['category_id']) ? intval($params['category_id']) : null;

// Sum query
$query = [($search ?? $categoryId), isset($categoryId) ? 'category' : 'search'];

// Get count of Lots
['data' => $lotsCount, 'error' => $errorLotsCount] = $lot->count($con, ...$query);

if ($errorLotsCount) {
  if (isset($errorLotsCount['message'])) {
    print($errorLotsCount['message']);
  }
}

$curPage = isset($params['page']) ? intval($params['page']) : 1;
$perPage = isset($params['per_page']) ? intval($params['per_page']) : 3;
$pagesCount = ceil($lotsCount / $perPage);
$offset = ($curPage - 1) * $perPage;
$pages = range(1, $pagesCount);

// Get list og Lots
['data' => $lots, 'error' => $error] = $lot->paginate($con, $perPage, $offset, ...$query);

if ($error) {
  if (isset($error['message'])) {
    print($error['message']);
  }
}
