<?php
require_once 'utils/helpers.php';
require_once 'includes/set.php';
require_once 'includes/auth.php';
require_once 'includes/categories.php';
require_once 'controllers/LotController.php';
require_once 'controllers/BetController.php';

$lotId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$lotId) {
  http_response_code(404);

  die();
}

// Get list of Lots
$lotCon = new LotController($con);

['data' => $lotData, 'error' => $error, 'success' => $success] = $lotCon->getItem($lotId);

if (!$lotData) {
  http_response_code(404);

  die();
}

$betCon = new BetController($con);

['data' => $bets, 'error' => $betsError] = $betCon->getList($lotData['id']);

if (isset($betsError['message'])) {
  print($betsError['message']);
}

// Title
$lotTitle = isset($lotData['title']) ? htmlspecialchars($lotData['title']) : 'Лот';
// Current price
$priceCurrent = isset($lotData['price_current']) ? $lotData['price_current'] : $lotData['price_start'];
// Price Step
$priceStep = $lotData['price_step'] ?? 0;
// Has expired earlier
$expired = ($expirationDate = date_create($lotData['expiration_date'])) <= date_create();

$pageContentData = [
  'title' => $lotTitle,
  'priceCurrent' => $priceCurrent,
  'priceStep' => $priceStep,
  'expired' => $expired,
  'lot' => $lotData,
  'bets' => $bets ?? [],
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userId' => $userId,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $price = $_POST['price'];

  $errors = [];

  if (!$isAuth) {
    $errors['price'] = 'Необходимо авторизоваться';
  } else if ($userId === $lotData['user_id']) {
    $errors['price'] = 'Нельзя сделать ставку на свой лот';
  } else if ($intPriceError = validateInt($price)) {
    $errors['price'] = $intPriceError;
  } else if (($price = intval($price)) < ($minBet = intval($priceCurrent) + intval($priceStep))) {
    $errors['price'] = 'Ставка не может быть меньше ' . $minBet;
  } else if ($expired) {
    $errors['price'] = 'Торги закрыты ' . date_format($expirationDate, 'd.m.y в H:m');
  } else if (isset($lotData['winner_bet_id'])) {
    $errors['price'] = 'Торги закрыты. Определен победитель.';
  }

  $errors = array_filter($errors);

  if (empty($errors)) {
    ['error' => $errorBet] = $betCon->create($lotId, $price, $userId);

    if (isset($errorBet['message'])) {
      $errors['price'] = $errorBet['message'];
    }
  }

  $pageContentData['errors'] = $errors;
}

$pageContent = includeTemplate('pages/lot.php', $pageContentData);

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
