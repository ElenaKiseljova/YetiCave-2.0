<?php
require_once 'utils/set.php';
require_once 'controllers/UserController.php';

session_start();

// Auth User Flag
$isAuth = false;

// User Name
$userName = null;

if (isset($_SESSION['user'])) {
  $userCon = new UserController();

  ['data' => $user] = $userCon->getBy($con, 'id', $_SESSION['user']['id']);

  if (!$user) {
    header('Location: /logout');
  } else {
    $isAuth = true;

    // Set user variables
    $userId = $user['id'];
    $userName = $user['name'];
  }
}
