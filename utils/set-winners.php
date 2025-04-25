<?php
error_reporting(E_ERROR);

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/set.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/LotController.php';

$lotCon = new LotController($con);

['error' => $error] = $lotCon->updateWinners();

if (isset($error['message'])) {
  echo '<h1>Много чего пошло не так...</h1>';

  var_dump($error['message']);
} else {
  echo '<h1>Победители установлены! Письма отправлены!</h1>';
}
